<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token. Uniquely named to avoid Pest's
 * global-function collision across API test files.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function userApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/users', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/users')->assertUnauthorized();
    });

    it('returns 403 without users.read', function () {
        $this->getJson('/api/v1/users', userApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists users in a paginated envelope without exposing passwords', function () {
        // The auth user itself (created by the bearer helper, and holding a role) is also
        // listed — the resource reads is_super_admin/permissions off the role, so this
        // proves the index eager-loads role and does not lazy-load-500 on a multi-row page.
        User::factory()->count(3)->create();

        $this->getJson('/api/v1/users', userApiBearer(['users.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'first_name', 'last_name', 'full_name', 'email',
                    'is_super_admin', 'permissions',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonMissing(['password']);
    });
});

describe('POST /api/v1/users', function () {
    it('returns 403 without users.create', function () {
        $this->postJson('/api/v1/users', [], userApiBearer(['users.read']))
            ->assertForbidden();
    });

    it('creates a user and never returns the password', function () {
        $response = $this->postJson('/api/v1/users', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ], userApiBearer(['users.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'first_name', 'email', 'is_super_admin']])
            ->assertJsonPath('data.email', 'jane@example.com')
            ->assertJsonMissingPath('data.password');

        $this->assertDatabaseHas('users', [
            'id' => $response->json('data.id'),
            'email' => 'jane@example.com',
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/users', [], userApiBearer(['users.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    });
});

describe('id-scoped user actions', function () {
    it('returns 404 for a missing user', function () {
        $this->getJson('/api/v1/users/999999', userApiBearer(['users.read']))
            ->assertNotFound();
    });

    it('shows a single user without the password', function () {
        $user = User::factory()->create(['first_name' => 'Main']);

        $this->getJson("/api/v1/users/{$user->id}", userApiBearer(['users.read']))
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.first_name', 'Main')
            ->assertJsonMissingPath('data.password');
    });

    it('allows an update that keeps the user\'s own email', function () {
        // Regression: on the {id} API route the unique-email rule must ignore THIS user,
        // otherwise a PUT that keeps the same email 422s against itself.
        $user = User::factory()->create([
            'email' => 'keep@example.com',
            'first_name' => 'Before',
        ]);

        $this->putJson("/api/v1/users/{$user->id}", [
            'first_name' => 'After',
            'last_name' => $user->last_name,
            'email' => 'keep@example.com',
        ], userApiBearer(['users.update']))
            ->assertOk()
            ->assertJsonPath('data.email', 'keep@example.com')
            ->assertJsonPath('data.first_name', 'After');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'After',
        ]);
    });

    it('assigns a role on update', function () {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $this->putJson("/api/v1/users/{$user->id}", [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role_id' => $role->id,
        ], userApiBearer(['users.update']))
            ->assertOk()
            ->assertJsonPath('data.role.id', $role->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $role->id,
        ]);
    });

    it('returns 403 without users.update', function () {
        $user = User::factory()->create();

        $this->putJson("/api/v1/users/{$user->id}", [
            'first_name' => 'Nope',
            'last_name' => $user->last_name,
            'email' => $user->email,
        ], userApiBearer(['users.read']))
            ->assertForbidden();
    });

    it('returns 403 without users.delete', function () {
        $user = User::factory()->create();

        $this->deleteJson("/api/v1/users/{$user->id}", [], userApiBearer(['users.read']))
            ->assertForbidden();
    });

    it('deletes a user', function () {
        $user = User::factory()->create();

        $this->deleteJson("/api/v1/users/{$user->id}", [], userApiBearer(['users.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'User deleted successfully');

        $this->assertModelMissing($user);
    });

    it('returns 404 when deleting a missing user', function () {
        $this->deleteJson('/api/v1/users/999999', [], userApiBearer(['users.delete']))
            ->assertNotFound();
    });
});
