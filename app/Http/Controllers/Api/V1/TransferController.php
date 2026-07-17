<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferStoreRequest;
use App\Http\Resources\TransferResource;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for transfers between accounts. Reuses the web module's
 * TransferService, Resource, and Form Request. Transfers are create-only:
 * this controller intentionally exposes list + show + create (no update/delete),
 * mirroring the web module and TransferRepository.
 *
 * Money-unit contract: `source_amount`, `destination_amount`, and `fee_amount`
 * are emitted in MAJOR units (rupees) by TransferResource, matching the accounts
 * API. The request `source_amount`/`destination_amount` are major units too;
 * TransferService converts them to minor units on write. Same-currency transfers
 * with a fee report the true source amount and `exchange_rate = 1.0` (#47).
 */
class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
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

    public function store(TransferStoreRequest $request): JsonResponse
    {
        // Every create-time business rule (same/invalid account, non-positive amount,
        // negative fee, insufficient balance) is enforced by TransferStoreRequest → 422,
        // so the service's InvalidArgumentException is unreachable from a validated request.
        $transfer = $this->transferService->createTransfer($request->validated());

        return (new TransferResource($transfer))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): TransferResource
    {
        // The repository's find() eager-loads every relation TransferResource renders.
        $transfer = $this->transferService->findTransfer($id);

        abort_if($transfer === null, 404, 'Transfer not found');

        return new TransferResource($transfer);
    }
}
