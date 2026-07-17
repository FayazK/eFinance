<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for accounts. Reuses the web module's AccountService,
 * Resource, and Form Requests; every id-scoped action returns a clean 404 for a
 * missing account. Access is enforced entirely by the route-level
 * `permission:accounts.*` middleware (mirroring the web flow).
 *
 * Money-unit contract: `current_balance` INPUT (store/update) is in MAJOR units
 * (rupees/dollars) — the shared Form Requests + AccountService::toMinor() convert
 * to the minor-unit (paisa) integer column. OUTPUT (AccountResource) exposes
 * `current_balance` in major units and `formatted_balance` for display. The
 * controller does no conversion.
 */
class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly TransactionService $transactionService
    ) {}

    /**
     * Paginated list of accounts. AccountResource exposes no relations, so there
     * is no lazy-load hazard here.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $this->accountService->getPaginatedAccounts(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['currency_code', 'type', 'is_active']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return AccountResource::collection($accounts);
    }

    public function store(AccountStoreRequest $request): JsonResponse
    {
        $account = $this->accountService->createAccount($request->validated());

        return (new AccountResource($account))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): AccountResource
    {
        return new AccountResource($this->findOrFail($id));
    }

    public function update(int $id, AccountUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        $account = $this->accountService->updateAccount($id, $request->validated());

        return (new AccountResource($account))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        $this->accountService->deleteAccount($id);

        return response()->json(['message' => 'Account deleted successfully']);
    }

    /**
     * Paginated per-account transaction ledger. Routed through
     * getPaginatedTransactions (eager-loads `account`) so TransactionResource's
     * always-emitted formatted_amount never triggers a lazy-load violation.
     */
    public function transactions(int $id, Request $request): AnonymousResourceCollection
    {
        $this->findOrFail($id);

        $transactions = $this->transactionService->getPaginatedTransactions(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: array_merge(
                ['account_id' => $id],
                $request->only(['category_id', 'type', 'date'])
            ),
            sortBy: $request->input('sort_by', 'date'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return TransactionResource::collection($transactions);
    }

    /**
     * Resolve an account or abort with a 404.
     */
    private function findOrFail(int $id): Account
    {
        $account = $this->accountService->findAccount($id);

        abort_if($account === null, 404, 'Account not found');

        return $account;
    }
}
