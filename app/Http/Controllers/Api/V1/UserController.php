<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for users — an admin-scoped module. Reuses the web module's
 * UserService and Form Requests; every id-scoped action returns a clean 404 for a
 * missing user. Access is enforced entirely by the route-level `permission:users.*`
 * middleware.
 *
 * Responses use Api\V1\UserResource, which surfaces the read-only `is_super_admin`
 * and `permissions` accessors and never exposes the password. Those accessors read
 * the user's `role`, so read paths eager-load it: `index()` loads it onto the whole
 * page (a role-bearing user on a multi-row list would otherwise lazy-load-500 under
 * preventLazyLoading), and show/store/update load it so the `whenLoaded('role')`
 * block renders. Role assignment is via `role_id` through the existing service.
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->getPaginatedUsers(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['is_active', 'created_at']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        $users->getCollection()->loadMissing('role');

        return UserResource::collection($users);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        $user->load('role');

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): UserResource
    {
        $user = $this->findOrFail($id);
        $user->load('role');

        return new UserResource($user);
    }

    public function update(int $id, UserUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $user = $this->userService->updateProfile($id, $request->validated());
        $user->load('role');

        return (new UserResource($user))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->userService->deleteUser($id);

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Resolve a user or abort with a 404.
     */
    private function findOrFail(int $id): User
    {
        $user = $this->userService->findUser($id);

        abort_if($user === null, 404, 'User not found');

        return $user;
    }
}
