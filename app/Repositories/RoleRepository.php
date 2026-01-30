<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    /**
     * Find a role by ID.
     */
    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Find a role by slug.
     */
    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    /**
     * Get the default role.
     */
    public function findDefault(): ?Role
    {
        return Role::where('is_default', true)->first();
    }

    /**
     * Get all roles.
     */
    public function all(): Collection
    {
        return Role::all();
    }

    /**
     * Get all roles with user count.
     */
    public function getAllWithUserCount(): Collection
    {
        return Role::withCount('users')->get();
    }

    /**
     * Create a new role.
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update a role.
     */
    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update($data);

        return $role->fresh();
    }

    /**
     * Delete a role.
     */
    public function delete(int $id): bool
    {
        $role = Role::findOrFail($id);

        return $role->delete();
    }

    /**
     * Paginate roles with optional filters.
     */
    public function paginate(
        int $perPage = 15,
        ?string $search = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Role::query()->withCount('users');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allowedSortColumns = ['name', 'slug', 'created_at', 'is_default', 'users_count'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get user IDs associated with a role.
     */
    public function getUserIdsByRole(int $roleId): array
    {
        $role = Role::findOrFail($roleId);

        return $role->users()->pluck('id')->toArray();
    }

    /**
     * Check if a role has any users assigned.
     */
    public function hasUsers(int $roleId): bool
    {
        $role = Role::findOrFail($roleId);

        return $role->users()->exists();
    }

    /**
     * Get roles excluding super admin for assignment.
     */
    public function getAssignableRoles(): Collection
    {
        return Role::where('slug', '!=', config('permissions.super_admin_slug'))
            ->orderBy('name')
            ->get();
    }
}
