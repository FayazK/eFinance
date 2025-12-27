<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TransferStoreRequest;
use App\Http\Resources\TransferResource;
use App\Services\AccountService;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService,
        private readonly AccountService $accountService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/transfers/index');
    }

    public function data(Request $request): AnonymousResourceCollection
    {
        $transfers = $this->transferService->getPaginatedTransfers(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['account_id', 'date']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return TransferResource::collection($transfers);
    }

    public function create(): Response
    {
        $accounts = $this->accountService->getActiveAccounts();

        return Inertia::render('dashboard/transfers/create', [
            'accounts' => $accounts->map(fn ($account) => [
                'id' => $account->id,
                'name' => $account->name,
                'currency_code' => $account->currency_code,
                'current_balance' => $account->balance_in_major_units,
                'formatted_balance' => $account->formatted_balance,
            ]),
        ]);
    }

    public function store(TransferStoreRequest $request): JsonResponse
    {
        $transfer = $this->transferService->createTransfer($request->validated());

        return response()->json([
            'message' => 'Transfer completed successfully',
            'data' => new TransferResource($transfer),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $transfer = $this->transferService->findTransfer($id);

        if (! $transfer) {
            return response()->json(['message' => 'Transfer not found'], 404);
        }

        return response()->json(new TransferResource($transfer));
    }
}
