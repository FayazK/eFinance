<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 routes
|--------------------------------------------------------------------------
|
| Token-authenticated REST API. The `/api` prefix and the `api` middleware
| group are applied by bootstrap/app.php. To introduce a future version,
| add a sibling `Route::prefix('v2')->group(...)` block below.
|
*/

Route::prefix('v1')->group(function () {
    // Public: exchange credentials for a Bearer token.
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api');

    // Token-authenticated surface. Per-route RBAC is enforced with the existing
    // `permission:` middleware (guard-agnostic CheckPermission), so it works
    // unchanged under auth:sanctum.
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('auth/tokens', [AuthController::class, 'tokensIndex']);
        Route::post('auth/tokens', [AuthController::class, 'tokensStore']);
        Route::delete('auth/tokens/{id}', [AuthController::class, 'tokensDestroy'])
            ->whereNumber('id');

        // Sample RBAC-protected route — proves 401 (no token) / 403 (missing
        // permission) / 200 in one place; doubles as an authenticated health probe.
        Route::get('ping', fn () => response()->json(['message' => 'pong']))
            ->middleware('permission:accounts.read');
    });
});
