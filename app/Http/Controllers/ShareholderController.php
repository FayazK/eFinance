<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ShareholderStoreRequest;
use App\Http\Requests\ShareholderUpdateRequest;
use App\Http\Resources\ShareholderResource;
use App\Services\ShareholderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShareholderController extends Controller
{
    public function __construct(
        private readonly ShareholderService $shareholderService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard/shareholders/index');
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
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

    public function store(ShareholderStoreRequest $request): JsonResponse
    {
        $shareholder = $this->shareholderService->createShareholder($request->validated());

        return response()->json([
            'message' => 'Shareholder created successfully',
            'data' => new ShareholderResource($shareholder),
        ], 201);
    }

    public function update(ShareholderUpdateRequest $request, int $id): JsonResponse
    {
        $shareholder = $this->shareholderService->updateShareholder($id, $request->validated());

        return response()->json([
            'message' => 'Shareholder updated successfully',
            'data' => new ShareholderResource($shareholder),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->shareholderService->deleteShareholder($id);

        return response()->json([
            'message' => 'Shareholder deleted successfully',
        ]);
    }

    public function validateEquity(): JsonResponse
    {
        $validation = $this->shareholderService->validateEquityTotal();

        return response()->json($validation);
    }
}
