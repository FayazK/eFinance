<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for employees. Reuses the web module's EmployeeService, Resource,
 * and Form Requests; every id-scoped action returns a clean 404 for a missing employee.
 * Access is enforced entirely by the route-level `permission:employees.*` middleware.
 *
 * Money-unit contract: the request `base_salary` is in MAJOR units (rupees) — EmployeeService
 * converts it ×100 to minor units (paisa) on write — and EmployeeResource emits `base_salary`
 * back in MAJOR units. The controller itself does no conversion (no double-convert).
 */
class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $employees = $this->employeeService->getPaginatedEmployees(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status']),
            sortBy: $request->input('sort_by', 'name'),
            sortDirection: $request->input('sort_direction', 'asc')
        );

        return EmployeeResource::collection($employees);
    }

    public function store(EmployeeStoreRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->validated());

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): EmployeeResource
    {
        // The service's find() eager-loads payrolls (guarded by whenLoaded in the resource).
        return new EmployeeResource($this->findOrFail($id));
    }

    public function update(int $id, EmployeeUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $employee = $this->employeeService->updateEmployee($id, $request->validated());

        return (new EmployeeResource($employee))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->employeeService->deleteEmployee($id);

        return response()->json(['message' => 'Employee deleted successfully']);
    }

    /**
     * Resolve an employee or abort with a 404.
     */
    private function findOrFail(int $id): Employee
    {
        $employee = $this->employeeService->findEmployee($id);

        abort_if($employee === null, 404, 'Employee not found');

        return $employee;
    }
}
