<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function find(int $id): ?Company;

    public function create(array $data): Company;

    public function update(int $id, array $data): Company;

    public function delete(int $id): bool;

    public function all(): Collection;

    public function paginateCompanies(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
