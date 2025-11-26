<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserDataTableRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Queries\UserDataTableQueryService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/users/index');
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/users/create');
    }

    public function data(UserDataTableRequest $request): UserCollection
    {
        $query = User::query();
        $paginatedUsers = (new UserDataTableQueryService($query, $request))->getResults();

        return new UserCollection($paginatedUsers);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUser($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(new UserDetailResource($user));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('dashboard/users/edit', [
            'user' => $user,
        ]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => new UserDetailResource($user),
        ], 201);
    }

    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->updateProfile($id, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserDetailResource($user),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->findUser($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $this->userService->deleteUser($id);

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
