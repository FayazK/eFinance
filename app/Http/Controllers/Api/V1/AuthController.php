<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateTokenRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Issue an API token for valid credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => __('auth.failed')], 401);
        }

        $user->load('role');
        $token = $user->createToken('api', $this->abilitiesFor($user));

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Return the authenticated user with resolved permissions.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('role'));
    }

    /**
     * List the authenticated user's tokens (never exposes the secret value).
     */
    public function tokensIndex(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()
            ->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);

        return response()->json(['data' => $tokens]);
    }

    /**
     * Create a named token, clamped to abilities the user actually holds.
     */
    public function tokensStore(CreateTokenRequest $request): JsonResponse
    {
        $user = $request->user()->load('role');
        $abilities = $this->resolveRequestedAbilities($user, $request->validated('abilities'));

        $token = $user->createToken($request->validated('name'), $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
        ], 201);
    }

    /**
     * Revoke one of the authenticated user's tokens by id.
     */
    public function tokensDestroy(Request $request, int $id): JsonResponse
    {
        $deleted = $request->user()->tokens()->whereKey($id)->delete();

        abort_if($deleted === 0, 404, 'Token not found.');

        return response()->json(['message' => 'Token revoked.']);
    }

    /**
     * The abilities granted to a login token: all for a super-admin, else the user's permissions.
     *
     * @return list<string>
     */
    private function abilitiesFor(User $user): array
    {
        return $user->is_super_admin ? ['*'] : $user->permissions;
    }

    /**
     * Resolve the abilities for a named token, clamped to what the user is allowed to grant.
     *
     * @param  list<string>|null  $requested
     * @return list<string>
     */
    private function resolveRequestedAbilities(User $user, ?array $requested): array
    {
        if ($user->is_super_admin) {
            return $requested ?: ['*'];
        }

        if ($requested === null) {
            return $user->permissions;
        }

        return array_values(array_intersect($requested, $user->permissions));
    }
}
