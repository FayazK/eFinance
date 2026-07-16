<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);
});

/**
 * JSON request headers carrying a Bearer token.
 *
 * @return array<string, string>
 */
function bearer(string $token): array
{
    return ['Authorization' => 'Bearer '.$token];
}

describe('POST /api/v1/auth/login', function () {
    it('returns a Bearer token and the user for valid credentials', function () {
        User::factory()->superAdmin()->create(['email' => 'admin@example.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'email', 'permissions']])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'admin@example.com');

        expect($response->json('token'))->not->toBeEmpty();
    });

    it('returns 422 when the input is invalid', function () {
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('returns 401 for wrong credentials', function () {
        User::factory()->superAdmin()->create(['email' => 'admin@example.com']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    });
});

describe('GET /api/v1/auth/me', function () {
    it('returns the authenticated user with resolved permissions', function () {
        $user = User::factory()->superAdmin()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/auth/me', bearer($token))
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonStructure(['data' => ['id', 'email', 'is_super_admin', 'permissions']]);
    });

    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    });
});

describe('POST /api/v1/auth/logout', function () {
    it('revokes the current token', function () {
        $user = User::factory()->superAdmin()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->postJson('/api/v1/auth/logout', [], bearer($token))->assertOk();
        expect($user->tokens()->count())->toBe(0);

        // Forgetting guards simulates a fresh request; the revoked token must not authenticate.
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/auth/me', bearer($token))->assertUnauthorized();
    });
});

describe('RBAC on protected routes (GET /api/v1/ping)', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/ping')->assertUnauthorized();
    });

    it('returns 403 when the user lacks the required permission', function () {
        $role = Role::factory()->create(['permissions' => ['expenses.read']]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $token = $user->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/ping', bearer($token))->assertForbidden();
    });

    it('returns 200 when the user has the required permission', function () {
        $role = Role::factory()->create(['permissions' => ['accounts.read']]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $token = $user->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/ping', bearer($token))
            ->assertOk()
            ->assertJsonPath('message', 'pong');
    });

    it('allows a super-admin through', function () {
        $user = User::factory()->superAdmin()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->getJson('/api/v1/ping', bearer($token))->assertOk();
    });
});

describe('Named token management (/api/v1/auth/tokens)', function () {
    it('creates, lists, and revokes named tokens', function () {
        $user = User::factory()->superAdmin()->create();
        $sessionToken = $user->createToken('session')->plainTextToken;

        // Create a named token.
        $created = $this->postJson('/api/v1/auth/tokens', ['name' => 'mcp-server'], bearer($sessionToken))
            ->assertCreated()
            ->assertJsonPath('token_type', 'Bearer');
        $newToken = $created->json('token');
        expect($newToken)->not->toBeEmpty();

        // The new token authenticates (forget guards so the header is re-resolved, not cached).
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/auth/me', bearer($newToken))->assertOk();

        // Listing shows both tokens and never leaks the secret value.
        $this->app['auth']->forgetGuards();
        $list = $this->getJson('/api/v1/auth/tokens', bearer($sessionToken))->assertOk();
        expect($list->json('data'))->toHaveCount(2);
        expect(json_encode($list->json('data')))->not->toContain($newToken);

        // Revoke the named token by id.
        $namedId = $user->tokens()->where('name', 'mcp-server')->value('id');
        $this->app['auth']->forgetGuards();
        $this->deleteJson("/api/v1/auth/tokens/{$namedId}", [], bearer($sessionToken))->assertOk();

        // Revoked token stops working.
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/auth/me', bearer($newToken))->assertUnauthorized();
    });

    it('rejects abilities outside the permission vocabulary', function () {
        $user = User::factory()->superAdmin()->create();
        $token = $user->createToken('session')->plainTextToken;

        $this->postJson('/api/v1/auth/tokens', [
            'name' => 'bad',
            'abilities' => ['not.a.real.ability'],
        ], bearer($token))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['abilities.0']);
    });
});

describe('API rate limiting', function () {
    it('returns 429 with JSON when the limit is exceeded', function () {
        // Tighten the limiter for this test so we can trip it in two requests.
        RateLimiter::for('api', fn () => Limit::perMinute(1)->by('rate-limit-test'));

        // First request consumes the single allowed hit.
        $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'y']);

        // Second request is throttled with a JSON envelope.
        $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'y'])
            ->assertTooManyRequests()
            ->assertJsonStructure(['message']);
    });
});
