<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistributionAdjustRequest;
use App\Http\Requests\DistributionProcessRequest;
use App\Http\Requests\DistributionStoreRequest;
use App\Http\Requests\DistributionUpdateRequest;
use App\Http\Resources\DistributionResource;
use App\Models\Distribution;
use App\Services\DistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * JSON REST surface for distributions. Reuses the web module's DistributionService,
 * Resources, and Form Requests; every id-scoped action returns a clean 404 for a
 * missing distribution. Business-rule violations raised by the service (equity not
 * 100%, editing/processing a non-draft, non-PKR account, insufficient balance) are
 * mapped to 422.
 *
 * Money-unit contract: OUTPUT money (DistributionResource) is in major units (rupees).
 * The two money INPUT fields — `manual_amount_pkr` (store) and `adjusted_amount`
 * (adjust-profit) — are minor units (paisa), reused verbatim from the shared Form
 * Requests exactly as the web frontend sends them; the controller does no conversion.
 */
class DistributionController extends Controller
{
    /**
     * Relations the resources render. Eager-loaded before serialization so
     * `lines[].transaction` (TransactionResource reads its account's currency,
     * #120) never triggers a lazy-load violation.
     *
     * @var list<string>
     */
    private const array RELATIONS = ['lines.shareholder', 'lines.transaction.account'];

    public function __construct(
        private readonly DistributionService $distributionService
    ) {}

    /**
     * Paginated list of distributions. Lines are loaded but their nested
     * shareholder/transaction are omitted here (only rendered on the single reads).
     */
    public function index(Request $request): AnonymousResourceCollection
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
        try {
            $distribution = $this->distributionService->createDistribution($request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new DistributionResource($distribution->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): DistributionResource
    {
        // The repository's find() already eager-loads self::RELATIONS.
        return new DistributionResource($this->findOrFail($id));
    }

    public function update(int $id, DistributionUpdateRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        try {
            $distribution = $this->distributionService->updateDistribution($id, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new DistributionResource($distribution->load(self::RELATIONS)))->response();
    }

    public function adjustProfit(int $id, DistributionAdjustRequest $request): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $distribution = $this->distributionService->adjustNetProfit(
                $id,
                $request->validated('adjusted_amount'),
                $request->validated('reason'),
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new DistributionResource($distribution->load(self::RELATIONS)))->response();
    }

    public function process(int $id, DistributionProcessRequest $request): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $distribution = $this->distributionService->processDistribution(
                $id,
                $request->validated('account_id'),
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new DistributionResource($distribution->load(self::RELATIONS)))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $this->distributionService->deleteDistribution($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Distribution deleted successfully']);
    }

    /**
     * Stream a shareholder's profit statement as an inline PDF, mirroring the web
     * module. A missing distribution or a shareholder not in it maps to a 404.
     */
    public function downloadStatement(int $id, int $shareholderId): Response
    {
        try {
            return $this->distributionService->generatePartnerStatement($id, $shareholderId);
        } catch (\InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Resolve a fully eager-loaded distribution or abort with a 404.
     */
    private function findOrFail(int $id): Distribution
    {
        $distribution = $this->distributionService->findDistribution($id);

        abort_if($distribution === null, 404, 'Distribution not found');

        return $distribution;
    }
}
