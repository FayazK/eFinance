<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    public function find(int $id): ?Employee;

    public function create(array $data): Employee;

    public function update(int $id, array $data): Employee;

    public function delete(int $id): bool;

    public function all(): Collection;

    public function getActiveEmployees(): Collection;

    public function paginateEmployees(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
