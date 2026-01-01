<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payroll;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayrollRepository
{
    public function find(int $id): ?Payroll
    {
        return Payroll::with(['employee', 'transaction'])->find($id);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function update(int $id, array $data): Payroll
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update($data);

        return $payroll->fresh();
    }

    public function delete(int $id): bool
    {
        $payroll = Payroll::findOrFail($id);

        return $payroll->delete();
    }

    public function checkExistingPayroll(int $month, int $year): bool
    {
        return Payroll::forMonth($month, $year)->exists();
    }

    public function getPayrollsForMonth(int $month, int $year): Collection
    {
        return Payroll::with(['employee', 'transaction'])
            ->forMonth($month, $year)
            ->orderBy('employee_id')
            ->get();
    }

    public function paginatePayrolls(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Payroll::with(['employee', 'transaction']);

        // Search by employee name
        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($filters) {
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['month'])) {
                $query->where('month', $filters['month']);
            }

            if (isset($filters['year'])) {
                $query->where('year', $filters['year']);
            }

            if (isset($filters['employee_id'])) {
                $query->where('employee_id', $filters['employee_id']);
            }
        }

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
}
