<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    public function find(int $id): ?Project;

    public function create(array $data): Project;

    public function update(int $id, array $data): Project;

    public function delete(int $id): bool;

    public function all(): Collection;

    public function paginateProjects(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
