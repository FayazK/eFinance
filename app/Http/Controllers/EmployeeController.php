<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    public function index(): Response
    {
        $employees = $this->employeeService->getPaginatedEmployees(100);

        return Inertia::render('dashboard/employees/index', [
            'employees' => EmployeeResource::collection($employees),
        ]);
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $employees = $this->employeeService->getPaginatedEmployees(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status', 'created_at']),
            sortBy: $request->input('sort_by', 'name'),
            sortDirection: $request->input('sort_direction', 'asc')
        );

        return EmployeeResource::collection($employees);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/employees/create');
    }

    public function store(EmployeeStoreRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->validated());

        return response()->json([
            'message' => 'Employee created successfully',
            'data' => new EmployeeResource($employee),
        ], 201);
    }

    public function show(Employee $employee): Response
    {
        return Inertia::render('dashboard/employees/show', [
            'employee' => new EmployeeResource($employee->load('payrolls')),
        ]);
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('dashboard/employees/edit', [
            'employee' => new EmployeeResource($employee),
        ]);
    }

    public function update(EmployeeUpdateRequest $request, Employee $employee): JsonResponse
    {
        $updatedEmployee = $this->employeeService->updateEmployee($employee->id, $request->validated());

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => new EmployeeResource($updatedEmployee),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->employeeService->deleteEmployee($id);

        return response()->json([
            'message' => 'Employee deleted successfully',
        ]);
    }
}
