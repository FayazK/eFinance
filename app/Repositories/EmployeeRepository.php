<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository
{
    public function find(int $id): ?Employee
    {
        return Employee::with('payrolls')->find($id);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);

        return $employee->fresh();
    }

    public function delete(int $id): bool
    {
        $employee = Employee::findOrFail($id);

        return $employee->delete();
    }

    public function all(): Collection
    {
        return Employee::query()
            ->orderBy('status', 'asc')
            ->orderBy('name')
            ->get();
    }

    public function getActiveEmployees(): Collection
    {
        return Employee::active()
            ->orderBy('name')
            ->get();
    }

    public function paginateEmployees(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Employee::query();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($filters) {
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }
}
