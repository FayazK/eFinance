<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExpenseRepositoryInterface
{
    public function find(int $id): ?Expense;

    public function create(array $data): Expense;

    public function update(int $id, array $data): Expense;

    public function delete(int $id): bool;

    public function paginateExpenses(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'expense_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function paginateRecurringExpenses(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator;

    public function getDueRecurringExpenses(): Collection;

    public function getQuarterExpensesTotal(int $year, int $quarter): int;

    public function getTotalExpensesByCategory(string $startDate, string $endDate): Collection;
}
