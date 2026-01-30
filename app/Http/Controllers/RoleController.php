<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    /**
     * Display the roles list page.
     */
    public function index(): Response
    {
        return Inertia::render('dashboard/roles/index');
    }

    /**
     * Get paginated roles data for DataTable.
     */
    public function data(Request $request): AnonymousResourceCollection
    {
        $roles = $this->roleService->getPaginatedRoles(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return RoleResource::collection($roles);
    }

    /**
     * Display the create role page.
     */
    public function create(): Response
    {
        return Inertia::render('dashboard/roles/create', [
            'permissionModules' => $this->roleService->getAllPermissions(),
        ]);
    }

    /**
     * Store a new role.
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return response()->json([
            'message' => 'Role created successfully',
            'data' => new RoleResource($role),
        ], 201);
    }

    /**
     * Display the edit role page.
     */
    public function edit(Role $role): Response
    {
        // Prevent editing super admin role
        if ($role->isSuperAdmin()) {
            abort(403, 'The super admin role cannot be modified.');
        }

        return Inertia::render('dashboard/roles/edit', [
            'role' => new RoleResource($role),
            'permissionModules' => $this->roleService->getAllPermissions(),
        ]);
    }

    /**
     * Update a role.
     */
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $updatedRole = $this->roleService->updateRole($role->id, $request->validated());

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => new RoleResource($updatedRole),
        ]);
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->deleteRole($role->id);

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Get roles for dropdown/select components.
     */
    public function assignable(): JsonResponse
    {
        $roles = $this->roleService->getAssignableRoles();

        return response()->json([
            'data' => RoleResource::collection($roles),
        ]);
    }
}
