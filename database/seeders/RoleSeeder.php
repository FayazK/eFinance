<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all permissions from config
        $allPermissions = $this->getAllPermissions();

        // Super Admin - has no permissions stored (bypasses all checks)
        Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access. Bypasses all permission checks.',
                'permissions' => [],
                'is_default' => false,
            ]
        );

        // Admin - has all permissions
        Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrative access with all permissions.',
                'permissions' => $allPermissions,
                'is_default' => false,
            ]
        );

        // Manager - all except users and roles
        $managerPermissions = array_filter($allPermissions, function ($permission) {
            return ! str_starts_with($permission, 'users.') && ! str_starts_with($permission, 'roles.');
        });

        Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Management access to most features except user and role management.',
                'permissions' => array_values($managerPermissions),
                'is_default' => false,
            ]
        );

        // Viewer - read-only access
        $viewerPermissions = array_filter($allPermissions, function ($permission) {
            return str_ends_with($permission, '.read');
        });

        Role::updateOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access to view all data.',
                'permissions' => array_values($viewerPermissions),
                'is_default' => true,
            ]
        );
    }

    /**
     * Get all permissions from config.
     *
     * @return array<string>
     */
    protected function getAllPermissions(): array
    {
        $permissions = [];
        $modules = config('permissions.modules', []);

        foreach ($modules as $module => $config) {
            foreach ($config['permissions'] as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return $permissions;
    }
}
