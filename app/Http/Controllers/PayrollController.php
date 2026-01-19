<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PayrollAdjustmentRequest;
use App\Http\Requests\PayrollGenerateRequest;
use App\Http\Requests\PayrollPaymentRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\PayrollResource;
use App\Models\Account;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService
    ) {}

    public function index(Request $request): Response
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $payrolls = $this->payrollService->getPayrollsForMonth((int) $month, (int) $year);

        $accounts = Account::where('is_active', true)
            ->whereIn('currency_code', ['PKR', 'USD'])
            ->get()
            ->groupBy('currency_code');

        return Inertia::render('dashboard/payroll/index', [
            'payrolls' => PayrollResource::collection($payrolls),
            'month' => (int) $month,
            'year' => (int) $year,
            'pkrAccounts' => AccountResource::collection($accounts->get('PKR', collect()))->resolve(),
            'usdAccounts' => AccountResource::collection($accounts->get('USD', collect()))->resolve(),
        ]);
    }

    public function data(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $payrolls = $this->payrollService->getPaginatedPayrolls(
            perPage: (int) $request->input('per_page', 15),
            search: $request->input('search'),
            filters: array_merge(
                $request->only(['status', 'employee_id']),
                ['month' => (int) $month, 'year' => (int) $year]
            ),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc')
        );

        return PayrollResource::collection($payrolls);
    }

    public function generate(PayrollGenerateRequest $request): JsonResponse
    {
        $payrolls = $this->payrollService->generatePayrollForMonth(
            $request->validated('month'),
            $request->validated('year')
        );

        return response()->json([
            'message' => 'Payroll generated successfully',
            'data' => PayrollResource::collection($payrolls),
        ], 201);
    }

    public function updateAdjustments(PayrollAdjustmentRequest $request, int $id): JsonResponse
    {
        $payroll = $this->payrollService->updatePayrollAdjustments($id, $request->validated());

        return response()->json([
            'message' => 'Adjustments saved successfully',
            'data' => new PayrollResource($payroll),
        ]);
    }

    public function pay(PayrollPaymentRequest $request): JsonResponse
    {
        $payrollIds = $request->validated('payroll_ids');
        $paymentData = $request->validated();

        $payments = $this->payrollService->payBatchPayrolls($payrollIds, $paymentData);

        return response()->json([
            'message' => 'Payroll paid successfully',
            'data' => PayrollResource::collection(collect($payments)),
        ]);
    }

    public function show(Payroll $payroll): Response
    {
        return Inertia::render('dashboard/payroll/show', [
            'payroll' => new PayrollResource($payroll->load(['employee', 'transaction'])),
        ]);
    }
}
