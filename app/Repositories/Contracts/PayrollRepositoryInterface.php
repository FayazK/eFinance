<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Payroll;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayrollRepositoryInterface
{
    public function find(int $id): ?Payroll;

    public function create(array $data): Payroll;

    public function update(int $id, array $data): Payroll;

    public function delete(int $id): bool;

    public function checkExistingPayroll(int $month, int $year): bool;

    public function getPayrollsForMonth(int $month, int $year): Collection;

    public function paginatePayrolls(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
