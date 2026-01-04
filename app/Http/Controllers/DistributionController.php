<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DistributionAdjustRequest;
use App\Http\Requests\DistributionProcessRequest;
use App\Http\Requests\DistributionStoreRequest;
use App\Http\Requests\DistributionUpdateRequest;
use App\Http\Resources\DistributionResource;
use App\Http\Resources\ShareholderResource;
use App\Models\Account;
use App\Models\Shareholder;
use App\Services\DistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DistributionController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService
    ) {}

    public function index(): Response
    {
        $pkrAccounts = Account::where('currency_code', 'PKR')->where('is_active', true)->get();

        return Inertia::render('dashboard/distributions/index', [
            'pkrAccounts' => $pkrAccounts,
        ]);
    }

    public function create(): Response
    {
        $pkrAccounts = Account::where('currency_code', 'PKR')
            ->where('is_active', true)
            ->get()
            ->map(fn ($account) => [
                ...$account->toArray(),
                'formatted_balance' => $account->formatted_balance,
            ]);

        $shareholders = Shareholder::where('is_active', true)->get();

        return Inertia::render('dashboard/distributions/create', [
            'pkrAccounts' => $pkrAccounts,
            'shareholders' => ShareholderResource::collection($shareholders)->resolve(),
        ]);
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $distributions = $this->distributionService->getPaginatedDistributions(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status']),
            sortBy: $request->input('sort_by', 'period_start'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return DistributionResource::collection($distributions);
    }

    public function store(DistributionStoreRequest $request): JsonResponse
    {
        $distribution = $this->distributionService->createDistribution($request->validated());

        $message = $request->validated('action') === 'process'
            ? 'Distribution created and processed successfully'
            : 'Distribution created successfully';

        return response()->json([
            'message' => $message,
            'data' => new DistributionResource($distribution),
        ], 201);
    }

    public function show(int $id): Response
    {
        $distribution = $this->distributionService->findDistribution($id);

        if (! $distribution) {
            abort(404);
        }

        $pkrAccounts = Account::where('currency_code', 'PKR')->where('is_active', true)->get();

        return Inertia::render('dashboard/distributions/show', [
            'distribution' => new DistributionResource($distribution->load(['lines.shareholder', 'lines.transaction'])),
            'pkrAccounts' => $pkrAccounts,
        ]);
    }

    public function update(DistributionUpdateRequest $request, int $id): JsonResponse
    {
        $distribution = $this->distributionService->updateDistribution($id, $request->validated());

        return response()->json([
            'message' => 'Distribution updated successfully',
            'data' => new DistributionResource($distribution),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->distributionService->deleteDistribution($id);

        return response()->json([
            'message' => 'Distribution deleted successfully',
        ]);
    }

    public function adjustProfit(DistributionAdjustRequest $request, int $id): JsonResponse
    {
        $distribution = $this->distributionService->adjustNetProfit(
            $id,
            $request->validated('adjusted_amount'),
            $request->validated('reason')
        );

        return response()->json([
            'message' => 'Net profit adjusted successfully',
            'data' => new DistributionResource($distribution),
        ]);
    }

    public function process(DistributionProcessRequest $request, int $id): JsonResponse
    {
        $distribution = $this->distributionService->processDistribution(
            $id,
            $request->validated('account_id')
        );

        return response()->json([
            'message' => 'Distribution processed successfully',
            'data' => new DistributionResource($distribution),
        ]);
    }

    public function downloadStatement(int $id, int $shareholderId): \Illuminate\Http\Response
    {
        return $this->distributionService->generatePartnerStatement($id, $shareholderId);
    }
}
