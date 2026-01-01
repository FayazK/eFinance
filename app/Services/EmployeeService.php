<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class EmployeeService
{
    public function __construct(
        private EmployeeRepository $employeeRepository
    ) {}

    public function createEmployee(array $data): Employee
    {
        // Convert salary from major units to minor units (paisa)
        if (isset($data['base_salary'])) {
            $data['base_salary_pkr'] = (int) ($data['base_salary'] * 100);
            unset($data['base_salary']);
        } else {
            throw new InvalidArgumentException('Base salary is required');
        }

        return $this->employeeRepository->create($data);
    }

    public function updateEmployee(int $employeeId, array $data): Employee
    {
        // Convert salary from major units to minor units if present
        if (isset($data['base_salary'])) {
            $data['base_salary_pkr'] = (int) ($data['base_salary'] * 100);
            unset($data['base_salary']);
        }

        return $this->employeeRepository->update($employeeId, $data);
    }

    public function terminateEmployee(int $employeeId, string $terminationDate): Employee
    {
        return $this->employeeRepository->update($employeeId, [
            'status' => 'terminated',
            'termination_date' => $terminationDate,
        ]);
    }

    public function getActiveEmployees(): Collection
    {
        return $this->employeeRepository->getActiveEmployees();
    }

    public function getPaginatedEmployees(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->employeeRepository->paginateEmployees(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function findEmployee(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function deleteEmployee(int $id): bool
    {
        return $this->employeeRepository->delete($id);
    }
}
