<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function find(int $id): ?Role;

    public function findBySlug(string $slug): ?Role;

    public function findDefault(): ?Role;

    public function all(): Collection;

    public function getAllWithUserCount(): Collection;

    public function create(array $data): Role;

    public function update(int $id, array $data): Role;

    public function delete(int $id): bool;

    public function paginate(
        int $perPage = 15,
        ?string $search = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function getUserIdsByRole(int $roleId): array;

    public function hasUsers(int $roleId): bool;

    public function getAssignableRoles(): Collection;
}
