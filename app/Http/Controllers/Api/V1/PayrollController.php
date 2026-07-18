<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollAdjustmentRequest;
use App\Http\Requests\PayrollGenerateRequest;
use App\Http\Requests\PayrollPaymentRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * JSON REST surface for payroll. Reuses the web module's PayrollService, Resource, and
 * Form Requests. Payroll is action-oriented (generate / pay / adjustments) rather than
 * plain CRUD — there is no store/destroy. Access is enforced by the route-level
 * `permission:payroll.*` middleware (read for list/show, update for the actions,
 * mirroring the web routes).
 *
 * Business-rule violations raised by the service (bad month, duplicate period, editing a
 * paid payroll, wrong-currency account, insufficient balance) are mapped to 422 — the
 * Form Requests re-validate the same conditions, so a 422 is normally raised before the
 * service runs; the catch is defence-in-depth on this money-out flow.
 *
 * Money-unit contract: amounts are stored in MINOR units (paisa). PayrollResource emits
 * `base_salary`/`bonus`/`deductions`/`net_payable` in MAJOR units (÷100); adjustment
 * `bonus`/`deductions` arrive in MAJOR units and are converted ×100 on write. The
 * controller itself does no conversion.
 */
class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $payrolls = $this->payrollService->getPaginatedPayrolls(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: $request->only(['status', 'employee_id', 'month', 'year']),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return PayrollResource::collection($payrolls);
    }

    public function show(int $id): PayrollResource
    {
        // The repository's find() eager-loads employee + transaction.
        return new PayrollResource($this->findOrFail($id));
    }

    public function generate(PayrollGenerateRequest $request): JsonResponse
    {
        try {
            $payrolls = $this->payrollService->generatePayrollForMonth(
                $request->validated('month'),
                $request->validated('year')
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // The service returns real persisted rows with `employee` eager-loaded (#70 fix).
        return PayrollResource::collection($payrolls)
            ->response()
            ->setStatusCode(201);
    }

    public function pay(PayrollPaymentRequest $request): JsonResponse
    {
        try {
            $payments = $this->payrollService->payBatchPayrolls(
                $request->validated('payroll_ids'),
                $request->validated()
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return PayrollResource::collection(collect($payments))->response();
    }

    public function updateAdjustments(int $id, PayrollAdjustmentRequest $request): JsonResponse
    {
        $this->findOrFail($id); // 404 first

        try {
            $payroll = $this->payrollService->updatePayrollAdjustments($id, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new PayrollResource($payroll))->response();
    }

    /**
     * Resolve a payroll or abort with a 404.
     */
    private function findOrFail(int $id): Payroll
    {
        $payroll = $this->payrollService->findPayroll($id);

        abort_if($payroll === null, 404, 'Payroll not found');

        return $payroll;
    }
}
