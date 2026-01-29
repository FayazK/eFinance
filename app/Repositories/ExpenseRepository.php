<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ExpenseRepository
{
    public function find(int $id): ?Expense
    {
        return Expense::with(['account', 'category', 'transaction', 'media'])->find($id);
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function update(int $id, array $data): Expense
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);

        return $expense->fresh(['account', 'category', 'transaction']);
    }

    public function delete(int $id): bool
    {
        $expense = Expense::findOrFail($id);

        return $expense->delete();
    }

    public function paginateExpenses(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'expense_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Expense::query()
            ->with(['account', 'category', 'transaction'])
            ->where('is_recurring', false); // Exclude recurring templates from main list

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    match ($column) {
                        'account_id' => $query->where('account_id', $value),
                        'category_id' => $query->where('category_id', $value),
                        'status' => $query->where('status', $value),
                        'expense_date' => is_array($value) && count($value) === 2
                            ? $query->whereBetween('expense_date', $value)
                            : null,
                        default => null,
                    };
                }
            }
        }

        $allowedSortColumns = ['expense_date', 'amount', 'vendor', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function paginateRecurringExpenses(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Expense::query()
            ->with(['account', 'category'])
            ->recurringTemplates();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('next_occurrence_date')->paginate($perPage);
    }

    public function getDueRecurringExpenses(): Collection
    {
        return Expense::recurringTemplates()
            ->where('next_occurrence_date', '<=', Carbon::today())
            ->get();
    }

    public function getQuarterExpensesTotal(int $year, int $quarter): int
    {
        $quarterStart = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfMonth();
        $quarterEnd = $quarterStart->copy()->addMonths(3)->subDay()->endOfDay();

        return Expense::processed()
            ->whereBetween('expense_date', [$quarterStart, $quarterEnd])
            ->sum('reporting_amount_pkr') ?? 0;
    }

    public function getTotalExpensesByCategory(string $startDate, string $endDate): Collection
    {
        return Expense::query()
            ->selectRaw('category_id, SUM(COALESCE(reporting_amount_pkr, amount)) as total')
            ->with('category')
            ->processed()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->get();
    }
}
