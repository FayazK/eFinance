<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DistributionController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ShareholderController;
use App\Http\Controllers\Api\V1\TransactionCategoryController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\TransferController;
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

        // Accounts — CRUD + per-account transaction ledger, mirroring the web module.
        // {id}/transactions nests under {id} (extra segment) so there is no static-vs-{id}
        // collision; {id} is numeric-constrained as a backstop.
        Route::get('accounts', [AccountController::class, 'index'])
            ->middleware('permission:accounts.read');
        Route::post('accounts', [AccountController::class, 'store'])
            ->middleware('permission:accounts.create');
        Route::get('accounts/{id}', [AccountController::class, 'show'])
            ->whereNumber('id')->middleware('permission:accounts.read');
        Route::get('accounts/{id}/transactions', [AccountController::class, 'transactions'])
            ->whereNumber('id')->middleware('permission:accounts.read');
        Route::put('accounts/{id}', [AccountController::class, 'update'])
            ->whereNumber('id')->middleware('permission:accounts.update');
        Route::delete('accounts/{id}', [AccountController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:accounts.delete');

        // Transactions — append-only ledger (list + create only, no update/delete).
        Route::get('transactions', [TransactionController::class, 'index'])
            ->middleware('permission:transactions.read');
        Route::post('transactions', [TransactionController::class, 'store'])
            ->middleware('permission:transactions.create');

        // Transaction Categories — list + create + update + delete (no show), mirroring the web module.
        // URL uses a hyphen; the permission key uses an underscore (transaction_categories.*), matching
        // config/permissions.php. {id} is numeric-constrained; no static sub-path to collide with it.
        Route::get('transaction-categories', [TransactionCategoryController::class, 'index'])
            ->middleware('permission:transaction_categories.read');
        Route::post('transaction-categories', [TransactionCategoryController::class, 'store'])
            ->middleware('permission:transaction_categories.create');
        Route::put('transaction-categories/{id}', [TransactionCategoryController::class, 'update'])
            ->whereNumber('id')->middleware('permission:transaction_categories.update');
        Route::delete('transaction-categories/{id}', [TransactionCategoryController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:transaction_categories.delete');

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

        // Distributions — CRUD + process/adjust/statement, mirroring the web module.
        // All custom actions nest under {id}, so there is no static-vs-{id} collision;
        // {id}/{shareholderId} are numeric-constrained as a backstop.
        Route::get('distributions', [DistributionController::class, 'index'])
            ->middleware('permission:distributions.read');
        Route::post('distributions', [DistributionController::class, 'store'])
            ->middleware('permission:distributions.create');
        Route::get('distributions/{id}', [DistributionController::class, 'show'])
            ->whereNumber('id')->middleware('permission:distributions.read');
        Route::put('distributions/{id}', [DistributionController::class, 'update'])
            ->whereNumber('id')->middleware('permission:distributions.update');
        Route::put('distributions/{id}/adjust-profit', [DistributionController::class, 'adjustProfit'])
            ->whereNumber('id')->middleware('permission:distributions.update');
        Route::post('distributions/{id}/process', [DistributionController::class, 'process'])
            ->whereNumber('id')->middleware('permission:distributions.update');
        Route::get('distributions/{id}/statements/{shareholderId}', [DistributionController::class, 'downloadStatement'])
            ->whereNumber('id')->whereNumber('shareholderId')->middleware('permission:distributions.read');
        Route::delete('distributions/{id}', [DistributionController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:distributions.delete');

        // Expenses — CRUD + process/void, mirroring the web module.
        // last-exchange-rate is a static-prefixed path and MUST precede expenses/{id}
        // to avoid a static-vs-{id} collision; the {id} routes are numeric-constrained
        // as a backstop. process/void nest under {id} (extra segment) — no collision.
        Route::get('expenses', [ExpenseController::class, 'index'])
            ->middleware('permission:expenses.read');
        Route::get('expenses/last-exchange-rate/{currency}', [ExpenseController::class, 'lastExchangeRate'])
            ->middleware('permission:expenses.read');
        Route::post('expenses', [ExpenseController::class, 'store'])
            ->middleware('permission:expenses.create');
        Route::get('expenses/{id}', [ExpenseController::class, 'show'])
            ->whereNumber('id')->middleware('permission:expenses.read');
        Route::put('expenses/{id}', [ExpenseController::class, 'update'])
            ->whereNumber('id')->middleware('permission:expenses.update');
        Route::post('expenses/{id}/process', [ExpenseController::class, 'process'])
            ->whereNumber('id')->middleware('permission:expenses.update');
        Route::post('expenses/{id}/void', [ExpenseController::class, 'void'])
            ->whereNumber('id')->middleware('permission:expenses.update');
        Route::delete('expenses/{id}', [ExpenseController::class, 'destroy'])
            ->whereNumber('id')->middleware('permission:expenses.delete');

        // Transfers — list + show + create only. Transfers are create-only in the web
        // module (TransferRepository has no update/delete), so the API adds none either.
        // {id} is numeric-constrained; there is no static-prefixed sub-path to collide with it.
        Route::get('transfers', [TransferController::class, 'index'])
            ->middleware('permission:transfers.read');
        Route::post('transfers', [TransferController::class, 'store'])
            ->middleware('permission:transfers.create');
        Route::get('transfers/{id}', [TransferController::class, 'show'])
            ->whereNumber('id')->middleware('permission:transfers.read');
    });
});
