<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
    /**
     * Get the cache key for user permissions.
     */
    protected function getPermissionsCacheKey(): string
    {
        return config('permissions.cache.prefix')."user:{$this->id}";
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (! $this->role) {
            return false;
        }

        if ($this->role->isSuperAdmin()) {
            return true;
        }

        if (! config('permissions.cache.enabled')) {
            return in_array($permission, $this->role->permissions ?? [], true);
        }

        $permissions = Cache::remember(
            $this->getPermissionsCacheKey(),
            config('permissions.cache.ttl'),
            fn () => $this->role->permissions ?? []
        );

        return in_array($permission, $permissions, true);
    }

    /**
     * Check if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions for the user.
     */
    public function getPermissionsAttribute(): array
    {
        if (! $this->role) {
            return [];
        }

        if ($this->role->isSuperAdmin()) {
            // Return all available permissions for super admin
            $allPermissions = [];
            foreach (config('permissions.modules', []) as $module => $config) {
                foreach ($config['permissions'] as $action) {
                    $allPermissions[] = "{$module}.{$action}";
                }
            }

            return $allPermissions;
        }

        if (! config('permissions.cache.enabled')) {
            return $this->role->permissions ?? [];
        }

        return Cache::remember(
            $this->getPermissionsCacheKey(),
            config('permissions.cache.ttl'),
            fn () => $this->role->permissions ?? []
        );
    }

    /**
     * Check if the user is a super admin.
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->role?->isSuperAdmin() ?? false;
    }

    /**
     * Clear the user's permission cache.
     */
    public function clearPermissionCache(): void
    {
        Cache::forget($this->getPermissionsCacheKey());
    }
}
