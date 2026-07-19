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
function roleApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/roles', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/roles')->assertUnauthorized();
    });

    it('returns 403 without roles.read', function () {
        $this->getJson('/api/v1/roles', roleApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists roles in a paginated envelope', function () {
        Role::factory()->count(3)->create();

        $this->getJson('/api/v1/roles', roleApiBearer(['roles.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'slug', 'description', 'permissions',
                    'is_default', 'is_super_admin', 'users_count',
                ]],
                'links',
                'meta',
            ]);
    });
});

describe('GET /api/v1/roles/assignable', function () {
    it('returns 403 without roles.read', function () {
        $this->getJson('/api/v1/roles/assignable', roleApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists assignable roles and excludes the super-admin role', function () {
        $superAdmin = Role::factory()->superAdmin()->create();
        $assignable = Role::factory()->create(['name' => 'Manager']);

        $response = $this->getJson('/api/v1/roles/assignable', roleApiBearer(['roles.read']))
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);

        $ids = collect($response->json('data'))->pluck('id');
        expect($ids)->toContain($assignable->id)
            ->and($ids)->not->toContain($superAdmin->id);
    });
});

describe('POST /api/v1/roles', function () {
    it('returns 403 without roles.create', function () {
        $this->postJson('/api/v1/roles', [], roleApiBearer(['roles.read']))
            ->assertForbidden();
    });

    it('creates a role and auto-generates the slug from the name', function () {
        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Regional Manager',
            'description' => 'Manages a region',
            'permissions' => ['clients.read', 'clients.update'],
        ], roleApiBearer(['roles.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'slug', 'permissions']])
            ->assertJsonPath('data.name', 'Regional Manager')
            ->assertJsonPath('data.slug', 'regional-manager');

        $this->assertDatabaseHas('roles', [
            'id' => $response->json('data.id'),
            'name' => 'Regional Manager',
        ]);
    });

    it('returns 422 for an unknown permission string', function () {
        $this->postJson('/api/v1/roles', [
            'name' => 'Broken',
            'permissions' => ['not_a_module.read'],
        ], roleApiBearer(['roles.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.0']);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/roles', [], roleApiBearer(['roles.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'permissions']);
    });
});

describe('PUT /api/v1/roles/{id}', function () {
    it('returns 404 for a missing role', function () {
        $this->putJson('/api/v1/roles/999999', [
            'name' => 'Nope',
            'permissions' => ['clients.read'],
        ], roleApiBearer(['roles.update']))
            ->assertNotFound();
    });

    it('returns 403 without roles.update', function () {
        $role = Role::factory()->create();

        $this->putJson("/api/v1/roles/{$role->id}", [
            'name' => 'Nope',
            'permissions' => ['clients.read'],
        ], roleApiBearer(['roles.read']))
            ->assertForbidden();
    });

    it('allows an update that keeps the role\'s own slug', function () {
        // Regression: on the {id} API route the unique-slug rule must ignore THIS role,
        // otherwise a PUT that keeps the same slug 422s against itself.
        $role = Role::factory()->create([
            'name' => 'Before',
            'slug' => 'keep-slug',
        ]);

        $this->putJson("/api/v1/roles/{$role->id}", [
            'name' => 'After',
            'slug' => 'keep-slug',
            'permissions' => ['clients.read'],
        ], roleApiBearer(['roles.update']))
            ->assertOk()
            ->assertJsonPath('data.slug', 'keep-slug')
            ->assertJsonPath('data.name', 'After');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'After',
        ]);
    });
});

describe('DELETE /api/v1/roles/{id}', function () {
    it('returns 403 without roles.delete', function () {
        $role = Role::factory()->create();

        $this->deleteJson("/api/v1/roles/{$role->id}", [], roleApiBearer(['roles.read']))
            ->assertForbidden();
    });

    it('deletes a role and returns JSON (not a redirect)', function () {
        $role = Role::factory()->create();

        $this->deleteJson("/api/v1/roles/{$role->id}", [], roleApiBearer(['roles.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Role deleted successfully');

        $this->assertModelMissing($role);
    });

    it('returns 404 when deleting a missing role', function () {
        $this->deleteJson('/api/v1/roles/999999', [], roleApiBearer(['roles.delete']))
            ->assertNotFound();
    });
});
