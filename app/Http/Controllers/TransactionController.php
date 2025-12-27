<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionCategoryResource;
use App\Http\Resources\TransactionResource;
use App\Services\AccountService;
use App\Services\TransactionCategoryService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly AccountService $accountService,
        private readonly TransactionCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/transactions/index');
    }

    public function data(Request $request): AnonymousResourceCollection
    {
        $transactions = $this->transactionService->getPaginatedTransactions(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['account_id', 'category_id', 'type', 'date']),
            sortBy: $request->input('sort_by', 'date'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return TransactionResource::collection($transactions);
    }

    public function create(): Response
    {
        $accounts = $this->accountService->getActiveAccounts();
        $categories = $this->categoryService->getAllCategories();

        return Inertia::render('dashboard/transactions/create', [
            'accounts' => $accounts->map(fn ($account) => [
                'id' => $account->id,
                'name' => $account->name,
                'currency_code' => $account->currency_code,
            ]),
            'categories' => TransactionCategoryResource::collection($categories),
        ]);
    }

    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());

        return response()->json([
            'message' => 'Transaction recorded successfully',
            'data' => new TransactionResource($transaction->load(['account', 'category'])),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionService->findTransaction($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(new TransactionResource($transaction));
    }
}
