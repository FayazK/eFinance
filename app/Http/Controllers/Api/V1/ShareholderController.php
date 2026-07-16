<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareholderStoreRequest;
use App\Http\Requests\ShareholderUpdateRequest;
use App\Http\Resources\ShareholderResource;
use App\Models\Shareholder;
use App\Services\ShareholderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for shareholders. Reuses the web module's ShareholderService,
 * Resource, and Form Requests; every id-scoped action returns a clean 404 for a
 * missing shareholder. Business-rule violations raised by the service (equity over
 * 100%, duplicate Office Reserve, delete with distribution history) are mapped to 422.
 */
class ShareholderController extends Controller
{
    public function __construct(
        private readonly ShareholderService $shareholderService
    ) {}

    /**
     * Paginated list of shareholders. The ShareholderResource exposes no relations,
     * so there is no lazy-load hazard here.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $shareholders = $this->shareholderService->getPaginatedShareholders(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['is_active', 'is_office_reserve']),
            sortBy: $request->input('sort_by', 'name'),
            sortDirection: $request->input('sort_direction', 'asc')
        );

        return ShareholderResource::collection($shareholders);
    }

    /**
     * Equity totals for the active cap table and whether they sum to 100%.
     * Cannot throw, so no exception mapping is needed.
     */
    public function validateEquity(): JsonResponse
    {
        return response()->json($this->shareholderService->validateEquityTotal());
    }

    public function store(ShareholderStoreRequest $request): JsonResponse
    {
        try {
            $shareholder = $this->shareholderService->createShareholder($request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ShareholderResource($shareholder))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ShareholderResource
    {
        return new ShareholderResource($this->findOrFail($id));
    }

    public function update(int $id, ShareholderUpdateRequest $request): JsonResponse
    {
        // 404 first so a missing shareholder never reaches the service's own not-found path.
        $this->findOrFail($id);

        try {
            $shareholder = $this->shareholderService->updateShareholder($id, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ShareholderResource($shareholder))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $this->shareholderService->deleteShareholder($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Shareholder deleted successfully']);
    }

    /**
     * Resolve a shareholder or abort with a 404.
     */
    private function findOrFail(int $id): Shareholder
    {
        $shareholder = $this->shareholderService->findShareholder($id);

        abort_if($shareholder === null, 404, 'Shareholder not found');

        return $shareholder;
    }
}
