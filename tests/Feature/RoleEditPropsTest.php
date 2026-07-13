<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

describe('Role edit page props', function () {
    beforeEach(function () {
        $this->withoutVite();
    });

    // Regression for #87 (same class as #81): RoleController@edit passed a bare RoleResource,
    // which Inertia wraps as { data: {...} }, but roles/edit.tsx reads the prop flat — so the
    // edit form rendered blank. The prop must be resolved to a flat array.
    test('edit page exposes the role prop unwrapped', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $role = Role::factory()->create([
            'name' => 'Accountant',
            'slug' => 'accountant',
            'permissions' => ['accounts.read', 'accounts.create'],
        ]);

        $this->get(route('roles.edit', $role))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/roles/edit')
                // Top-level prop is flat (would be under `role.data` if wrapped).
                ->where('role.name', 'Accountant')
                ->where('role.slug', 'accountant')
                ->has('role.permissions', 2)
            );
    });
});
