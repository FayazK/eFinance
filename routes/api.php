<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ShareholderController;
use App\Http\Controllers\Api\V1\TransactionController;
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

        // Transactions — append-only ledger (list + create only, no update/delete).
        Route::get('transactions', [TransactionController::class, 'index'])
            ->middleware('permission:transactions.read');
        Route::post('transactions', [TransactionController::class, 'store'])
            ->middleware('permission:transactions.create');

        // Invoices — full CRUD + custom actions, mirroring the web module.
        Route::get('invoices', [InvoiceController::class, 'index'])
            ->middleware('permission:invoices.read');
        Route::post('invoices', [InvoiceController::class, 'store'])
            ->middleware('permission:invoices.create');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])
            ->whereNumber('id')->middleware('permission:invoices.read');
        Route::get('invoices/{id}/pdf', [InvoiceController::class, 'pdf'])
            ->whereNumber('id')->middleware('permission:invoices.read');
        Route::put('invoices/{id}', [InvoiceController::class, 'update'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::put('invoices/{id}/due-date', [InvoiceController::class, 'updateDueDate'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::post('invoices/{id}/record-payment', [InvoiceController::class, 'recordPayment'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::post('invoices/{id}/change-status', [InvoiceController::class, 'changeStatus'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::post('invoices/{id}/send-email', [InvoiceController::class, 'sendEmail'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::post('invoices/{id}/void', [InvoiceController::class, 'void'])
            ->whereNumber('id')->middleware('permission:invoices.update');
        Route::delete('invoices/{id}', [InvoiceController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:invoices.delete');

        // Shareholders — CRUD + equity validation, mirroring the web module.
        // validate-equity is static and MUST precede shareholders/{id} (both GET) to avoid
        // the #113 static-vs-{id} collision; the {id} routes are numeric-constrained as a backstop.
        Route::get('shareholders', [ShareholderController::class, 'index'])
            ->middleware('permission:shareholders.read');
        Route::get('shareholders/validate-equity', [ShareholderController::class, 'validateEquity'])
            ->middleware('permission:shareholders.read');
        Route::post('shareholders', [ShareholderController::class, 'store'])
            ->middleware('permission:shareholders.create');
        Route::get('shareholders/{id}', [ShareholderController::class, 'show'])
            ->whereNumber('id')->middleware('permission:shareholders.read');
        Route::put('shareholders/{id}', [ShareholderController::class, 'update'])
            ->whereNumber('id')->middleware('permission:shareholders.update');
        Route::delete('shareholders/{id}', [ShareholderController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:shareholders.delete');
    });
});
