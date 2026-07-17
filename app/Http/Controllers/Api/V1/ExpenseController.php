<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Http\Requests\ExpenseVoidRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for expenses. Reuses the web module's ExpenseService, Resource,
 * and Form Requests; every id-scoped action returns a clean 404 for a missing expense.
 * Business-rule violations raised by the service (editing/processing/voiding/deleting an
 * expense in the wrong state, currency mismatch, insufficient balance) are mapped to 422.
 *
 * Money-unit contract: `amount` and `reporting_amount_pkr` are emitted in MINOR units
 * (paisa) — the raw stored integers — while `formatted_amount` / `formatted_reporting_amount`
 * are major-unit display strings. The request `amount` is in MAJOR units (rupees);
 * ExpenseService converts it ×100 on write. The controller itself does no conversion.
 */
class ExpenseController extends Controller
{
    /**
     * Relations ExpenseResource renders. Eager-loaded before serialization so no
     * relation access triggers a lazy-load violation (preventLazyLoading is on in dev).
     *
     * @var list<string>
     */
    private const array RELATIONS = ['account', 'category', 'transaction', 'media'];

    public function __construct(
        private readonly ExpenseService $expenseService,
        private readonly MediaService $mediaService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $expenses = $this->expenseService->getPaginatedExpenses(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status', 'account_id', 'category_id']),
            sortBy: $request->input('sort_by', 'expense_date'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return ExpenseResource::collection($expenses);
    }

    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $expense = $request->boolean('is_recurring')
            ? $this->expenseService->createRecurringExpense($validated)
            : $this->expenseService->createDraftExpense($validated);

        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $receipt) {
                $this->mediaService->addMedia($expense, $receipt, 'receipts');
            }
        }

        return (new ExpenseResource($expense->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ExpenseResource
    {
        // The repository's find() already eager-loads self::RELATIONS.
        return new ExpenseResource($this->findOrFail($id));
    }

    /**
     * Note: ExpenseUpdateRequest::authorize() 403s a missing or non-draft expense during
     * request resolution — before this body runs — so a clean 404 is not reachable here
     * (the same trade-off Api\V1\InvoiceController::update accepts).
     */
    public function update(ExpenseUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $expense = $this->expenseService->updateExpense($id, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ExpenseResource($expense->load(self::RELATIONS)))->response();
    }

    public function process(int $id): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        try {
            $expense = $this->expenseService->processExpense($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ExpenseResource($expense->load(self::RELATIONS)))->response();
    }

    public function void(int $id, ExpenseVoidRequest $request): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $expense = $this->expenseService->voidExpense($id, $request->validated()['void_reason']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ExpenseResource($expense->load(self::RELATIONS)))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findOrFail($id);

        try {
            $this->expenseService->deleteExpense($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    public function lastExchangeRate(string $currency): JsonResponse
    {
        return response()->json([
            'rate' => $this->expenseService->getLastExchangeRate($currency),
        ]);
    }

    /**
     * Resolve a fully eager-loaded expense or abort with a 404.
     */
    private function findOrFail(int $id): Expense
    {
        $expense = $this->expenseService->findExpense($id);

        abort_if($expense === null, 404, 'Expense not found');

        return $expense;
    }
}
