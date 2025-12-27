<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly TransactionService $transactionService
    ) {}

    public function index(): Response
    {
        $accounts = $this->accountService->getAllAccounts();
        $netWorthData = $this->accountService->calculateTotalNetWorth();

        return Inertia::render('dashboard/accounts/index', [
            'accounts' => AccountResource::collection($accounts),
            'netWorth' => 'PKR '.number_format($netWorthData['total_pkr'], 2),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/accounts/create');
    }

    public function store(AccountStoreRequest $request): JsonResponse
    {
        $account = $this->accountService->createAccount($request->validated());

        return response()->json([
            'message' => 'Account created successfully',
            'data' => new AccountResource($account),
        ], 201);
    }

    public function show(Account $account): Response
    {
        return Inertia::render('dashboard/accounts/show', [
            'account' => new AccountResource($account),
        ]);
    }

    public function edit(Account $account): Response
    {
        return Inertia::render('dashboard/accounts/edit', [
            'account' => new AccountResource($account),
        ]);
    }

    public function update(AccountUpdateRequest $request, Account $account): JsonResponse
    {
        $updatedAccount = $this->accountService->updateAccount($account->id, $request->validated());

        return response()->json([
            'message' => 'Account updated successfully',
            'data' => new AccountResource($updatedAccount),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $account = $this->accountService->findAccount($id);

        if (! $account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $this->accountService->deleteAccount($id);

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }

    public function transactions(Request $request, Account $account): AnonymousResourceCollection
    {
        $transactions = $this->transactionService->getPaginatedTransactions(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: array_merge(
                ['account_id' => $account->id],
                $request->only(['category_id', 'type', 'date'])
            ),
            sortBy: $request->input('sort_by', 'date'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return TransactionResource::collection($transactions);
    }
}
