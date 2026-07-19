<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for roles — an admin-scoped RBAC module. Reuses the web module's
 * RoleService, Form Requests, and RoleResource; access is enforced entirely by the
 * route-level `permission:roles.*` middleware.
 *
 * Contrast the web RoleController: `destroy()` here returns a JSON acknowledgement
 * (not an Inertia redirect). Id-scoped actions resolve a 404 for a missing role via
 * findOrFail; the service's super-admin and has-users guards surface as 422 JSON
 * (ValidationException) automatically. There is no public `show` (mirroring the web,
 * which exposes only an edit page). `RoleResource` touches no relations — permissions
 * is a JSON column and is_super_admin is a computed append — so no eager-loading.
 */
class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $roles = $this->roleService->getPaginatedRoles(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return RoleResource::collection($roles);
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, RoleUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $role = $this->roleService->updateRole($id, $request->validated());

        return (new RoleResource($role))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->roleService->deleteRole($id);

        return response()->json(['message' => 'Role deleted successfully']);
    }

    /**
     * Roles available for assignment (excludes the super-admin role).
     */
    public function assignable(): AnonymousResourceCollection
    {
        return RoleResource::collection($this->roleService->getAssignableRoles());
    }

    /**
     * Resolve a role or abort with a 404.
     */
    private function findOrFail(int $id): Role
    {
        $role = $this->roleService->findRole($id);

        abort_if($role === null, 404, 'Role not found');

        return $role;
    }
}
