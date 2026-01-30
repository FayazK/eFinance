<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {}

    /**
     * Get all permission modules grouped with their permissions.
     *
     * @return array<string, array{label: string, permissions: array<string>}>
     */
    public function getAllPermissions(): array
    {
        return config('permissions.modules', []);
    }

    /**
     * Get all available permission strings.
     *
     * @return array<string>
     */
    public function getAllPermissionStrings(): array
    {
        $permissions = [];
        foreach ($this->getAllPermissions() as $module => $config) {
            foreach ($config['permissions'] as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return $permissions;
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data): Role
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Handle is_default - unset other defaults if setting this as default
        if ($data['is_default'] ?? false) {
            Role::where('is_default', true)->update(['is_default' => false]);
        }

        return $this->roleRepository->create($data);
    }

    /**
     * Update a role.
     *
     * @throws ValidationException
     */
    public function updateRole(int $id, array $data): Role
    {
        $role = $this->roleRepository->find($id);

        if (! $role) {
            throw ValidationException::withMessages([
                'role' => ['Role not found.'],
            ]);
        }

        // Prevent editing super admin role
        if ($role->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'role' => ['The super admin role cannot be modified.'],
            ]);
        }

        // Handle is_default - unset other defaults if setting this as default
        if ($data['is_default'] ?? false) {
            Role::where('id', '!=', $id)->where('is_default', true)->update(['is_default' => false]);
        }

        $updatedRole = $this->roleRepository->update($id, $data);

        // Clear permission cache for all users with this role
        $this->clearCacheForRoleUsers($id);

        return $updatedRole;
    }

    /**
     * Delete a role.
     *
     * @throws ValidationException
     */
    public function deleteRole(int $id): bool
    {
        $role = $this->roleRepository->find($id);

        if (! $role) {
            throw ValidationException::withMessages([
                'role' => ['Role not found.'],
            ]);
        }

        // Prevent deleting super admin role
        if ($role->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'role' => ['The super admin role cannot be deleted.'],
            ]);
        }

        // Check if role has users
        if ($this->roleRepository->hasUsers($id)) {
            throw ValidationException::withMessages([
                'role' => ['Cannot delete a role that has users assigned. Please reassign users first.'],
            ]);
        }

        return $this->roleRepository->delete($id);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRoleToUser(int $userId, ?int $roleId): User
    {
        $user = User::findOrFail($userId);

        // Clear old role cache
        $user->clearPermissionCache();

        $user->update(['role_id' => $roleId]);

        return $user->fresh();
    }

    /**
     * Find a role by ID.
     */
    public function findRole(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    /**
     * Find a role by slug.
     */
    public function findRoleBySlug(string $slug): ?Role
    {
        return $this->roleRepository->findBySlug($slug);
    }

    /**
     * Get the default role.
     */
    public function getDefaultRole(): ?Role
    {
        return $this->roleRepository->findDefault();
    }

    /**
     * Get all roles.
     */
    public function getAllRoles(): Collection
    {
        return $this->roleRepository->all();
    }

    /**
     * Get all roles with user count.
     */
    public function getAllRolesWithUserCount(): Collection
    {
        return $this->roleRepository->getAllWithUserCount();
    }

    /**
     * Get paginated roles.
     */
    public function getPaginatedRoles(
        int $perPage = 15,
        ?string $search = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->roleRepository->paginate($perPage, $search, $sortBy, $sortDirection);
    }

    /**
     * Get roles available for assignment (excludes super admin).
     */
    public function getAssignableRoles(): Collection
    {
        return $this->roleRepository->getAssignableRoles();
    }

    /**
     * Clear permission cache for all users with a specific role.
     */
    protected function clearCacheForRoleUsers(int $roleId): void
    {
        $userIds = $this->roleRepository->getUserIdsByRole($roleId);
        $cachePrefix = config('permissions.cache.prefix');

        foreach ($userIds as $userId) {
            Cache::forget("{$cachePrefix}user:{$userId}");
        }
    }
}
