<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\Shareholder;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function shareholderApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/shareholders', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/shareholders')->assertUnauthorized();
    });

    it('returns 403 without shareholders.read', function () {
        $this->getJson('/api/v1/shareholders', shareholderApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists shareholders in a paginated envelope', function () {
        Shareholder::factory()->count(3)->create();

        $this->getJson('/api/v1/shareholders', shareholderApiBearer(['shareholders.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'email', 'equity_percentage', 'formatted_equity',
                    'is_office_reserve', 'is_human_partner', 'is_active', 'notes',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/shareholders', function () {
    it('returns 403 without shareholders.create', function () {
        $this->postJson('/api/v1/shareholders', [
            'name' => 'Acme Holdings',
            'equity_percentage' => 25,
        ], shareholderApiBearer(['shareholders.read']))
            ->assertForbidden();
    });

    it('creates a shareholder', function () {
        $this->postJson('/api/v1/shareholders', [
            'name' => 'Acme Holdings',
            'equity_percentage' => 25,
        ], shareholderApiBearer(['shareholders.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'equity_percentage', 'formatted_equity']])
            ->assertJsonPath('data.name', 'Acme Holdings');

        $this->assertDatabaseHas('shareholders', ['name' => 'Acme Holdings']);
    });

    it('returns 422 when the new equity would exceed 100%', function () {
        Shareholder::factory()->create(['equity_percentage' => 90, 'is_active' => true]);

        $this->postJson('/api/v1/shareholders', [
            'name' => 'Over Cap',
            'equity_percentage' => 20,
        ], shareholderApiBearer(['shareholders.create']))
            ->assertStatus(422);
    });
});

describe('GET /api/v1/shareholders/validate-equity', function () {
    it('reports valid when active equity sums to 100%', function () {
        Shareholder::factory()->create(['equity_percentage' => 60, 'is_active' => true]);
        Shareholder::factory()->create(['equity_percentage' => 40, 'is_active' => true]);

        $this->getJson('/api/v1/shareholders/validate-equity', shareholderApiBearer(['shareholders.read']))
            ->assertOk()
            ->assertJsonStructure(['total', 'is_valid', 'message'])
            ->assertJsonPath('is_valid', true);
    });

    it('reports invalid when active equity is below 100%', function () {
        Shareholder::factory()->create(['equity_percentage' => 60, 'is_active' => true]);

        $this->getJson('/api/v1/shareholders/validate-equity', shareholderApiBearer(['shareholders.read']))
            ->assertOk()
            ->assertJsonPath('is_valid', false)
            ->assertSee('60%');
    });
});

describe('id-scoped shareholder actions', function () {
    it('returns 404 for a missing shareholder', function () {
        $this->getJson('/api/v1/shareholders/999999', shareholderApiBearer(['shareholders.read']))
            ->assertNotFound();
    });

    it('updates a shareholder', function () {
        $shareholder = Shareholder::factory()->create(['equity_percentage' => 50, 'is_active' => true]);

        $this->putJson("/api/v1/shareholders/{$shareholder->id}", [
            'equity_percentage' => 30,
        ], shareholderApiBearer(['shareholders.update']))
            ->assertOk()
            ->assertJsonPath('data.equity_percentage', '30.00');

        $this->assertDatabaseHas('shareholders', ['id' => $shareholder->id, 'equity_percentage' => 30]);
    });

    it('soft-deletes a shareholder', function () {
        $shareholder = Shareholder::factory()->create();

        $this->deleteJson("/api/v1/shareholders/{$shareholder->id}", [], shareholderApiBearer(['shareholders.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Shareholder deleted successfully');

        $this->assertSoftDeleted('shareholders', ['id' => $shareholder->id]);
    });
});
