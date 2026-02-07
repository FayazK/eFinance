<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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


        // HR - expenses module only
        $hrPermissions = array_filter($allPermissions, function ($permission) {
            return str_starts_with($permission, 'expenses.');
        });

        $hrRole = Role::updateOrCreate(
            ['slug' => 'hr'],
            [
                'name' => 'HR',
                'description' => 'Access to expenses module only.',
                'permissions' => array_values($hrPermissions),
                'is_default' => false,
            ]
        );

        // Viewer - read-only access
        $viewerPermissions = array_filter($allPermissions, function ($permission) {
            return str_ends_with($permission, '.read');
        });



        // Assign admin role to info@fayazk.com
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            User::where('email', '!=', 'hr@empowerbits.com')
                ->update(['role_id' => $adminRole->id]);
        }

        // Create HR user and assign HR role
        User::updateOrCreate(
            ['email' => 'hr@empowerbits.com'],
            [
                'first_name' => 'HR',
                'last_name' => 'User',
                'password' => '@Password1',
                'is_active' => true,
                'role_id' => $hrRole->id,
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
