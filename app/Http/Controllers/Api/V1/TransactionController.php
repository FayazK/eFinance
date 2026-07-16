<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Transactions are an append-only ledger: this controller intentionally
 * exposes only list + create (no update or delete), mirroring the web module.
 */
class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    /**
     * Paginated list of ledger postings. Amounts are in major units; `type`
     * is `credit` (income) or `debit` (expense). Mirrors the web `data()` contract.
     */
    public function index(Request $request): AnonymousResourceCollection
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

    /**
     * Record a posting (income = `credit`, expense = `debit`). The service
     * converts the major-unit amount to minor units and updates the account balance.
     */
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());

        // Load account + category so formatted_amount serializes without a lazy-load violation.
        return (new TransactionResource($transaction->load(['account', 'category'])))
            ->response()
            ->setStatusCode(201);
    }
}
