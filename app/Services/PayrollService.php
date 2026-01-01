<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\PayrollAdjusted;
use App\Events\PayrollGenerated;
use App\Events\PayrollPaid;
use App\Models\Payroll;
use App\Models\TransactionCategory;
use App\Repositories\AccountRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PayrollRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayrollService
{
    public function __construct(
        private PayrollRepository $payrollRepository,
        private EmployeeRepository $employeeRepository,
        private AccountRepository $accountRepository,
        private TransactionService $transactionService
    ) {}

    /**
     * Generate payroll for all active employees for a specific month
     */
    public function generatePayrollForMonth(int $month, int $year): Collection
    {
        // Validate month and year
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Month must be between 1 and 12');
        }

        // Check if payroll already exists for this period
        if ($this->payrollRepository->checkExistingPayroll($month, $year)) {
            throw new InvalidArgumentException('Payroll for this period already exists');
        }

        return DB::transaction(function () use ($month, $year) {
            $employees = $this->employeeRepository->getActiveEmployees();
            $payrolls = [];

            foreach ($employees as $employee) {
                $payroll = $this->payrollRepository->create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'base_salary' => $employee->base_salary_pkr, // Snapshot
                    'bonus' => 0,
                    'deductions' => 0,
                    'net_payable' => $employee->base_salary_pkr,
                    'status' => 'pending',
                ]);

                $payrolls[] = $payroll;
            }

            // Convert array to Eloquent Collection
            $payrollCollection = Payroll::hydrate($payrolls)->load(['employee']);

            // Fire event
            event(new PayrollGenerated($month, $year, $payrollCollection));

            return $payrollCollection;
        });
    }

    /**
     * Update bonuses and deductions for a payroll
     */
    public function updatePayrollAdjustments(int $payrollId, array $data): Payroll
    {
        $payroll = $this->payrollRepository->find($payrollId);

        if (! $payroll) {
            throw new InvalidArgumentException('Payroll not found');
        }

        // Validate status is pending
        if ($payroll->status !== 'pending') {
            throw new InvalidArgumentException('Cannot edit paid payroll');
        }

        // Convert to minor units
        $updateData = [];

        if (isset($data['bonus'])) {
            $updateData['bonus'] = (int) ($data['bonus'] * 100);
        }

        if (isset($data['deductions'])) {
            $updateData['deductions'] = (int) ($data['deductions'] * 100);
        }

        if (isset($data['notes'])) {
            $updateData['notes'] = $data['notes'];
        }

        $payroll = $this->payrollRepository->update($payrollId, $updateData);

        // Fire event
        event(new PayrollAdjusted($payroll));

        return $payroll;
    }

    /**
     * Pay multiple payrolls in batch
     */
    public function payBatchPayrolls(array $payrollIds, array $data): array
    {
        return DB::transaction(function () use ($payrollIds, $data) {
            // Load all payrolls
            $payrolls = Payroll::with('employee')->whereIn('id', $payrollIds)->get();

            if ($payrolls->count() !== count($payrollIds)) {
                throw new InvalidArgumentException('Some payroll records not found');
            }

            // Validate all are pending
            foreach ($payrolls as $payroll) {
                if ($payroll->status !== 'pending') {
                    throw new InvalidArgumentException("Payroll #{$payroll->id} has already been paid");
                }
            }

            // Get account
            $account = $this->accountRepository->find($data['account_id']);

            if (! $account) {
                throw new InvalidArgumentException('Account not found');
            }

            // Validate account is PKR
            if ($account->currency_code !== 'PKR') {
                throw new InvalidArgumentException('Payroll can only be paid from PKR accounts');
            }

            // Calculate total needed
            $totalNeeded = $payrolls->sum('net_payable');

            // HARD BLOCK: Validate balance
            if ($account->current_balance < $totalNeeded) {
                throw new InvalidArgumentException('Insufficient balance. Please transfer funds first.');
            }

            // Pay each payroll
            $payments = [];
            foreach ($payrolls as $payroll) {
                $payments[] = $this->paySinglePayroll($payroll, $data);
            }

            return $payments;
        });
    }

    /**
     * Pay a single payroll
     */
    private function paySinglePayroll(Payroll $payroll, array $data): Payroll
    {
        // Create expense transaction
        $transaction = $this->transactionService->createTransaction([
            'account_id' => $data['account_id'],
            'category_id' => $this->getSalariesCategoryId(),
            'reference_type' => Payroll::class,
            'reference_id' => $payroll->id,
            'type' => 'debit',
            'amount' => $payroll->net_payable / 100, // Service converts to minor units
            'description' => "Salary: {$payroll->employee->name} - {$payroll->period_label}",
            'date' => $data['payment_date'] ?? now()->format('Y-m-d'),
        ]);

        // Update payroll
        $payroll = $this->payrollRepository->update($payroll->id, [
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transaction->id,
        ]);

        // Fire event
        event(new PayrollPaid($payroll));

        return $payroll->load(['employee', 'transaction']);
    }

    /**
     * Get or create "Salaries & Wages" category
     */
    private function getSalariesCategoryId(): int
    {
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'Salaries & Wages', 'type' => 'expense'],
            ['color' => 'blue']
        );

        return $category->id;
    }

    /**
     * Get paginated payrolls
     */
    public function getPaginatedPayrolls(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ) {
        return $this->payrollRepository->paginatePayrolls(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    /**
     * Find a payroll by ID
     */
    public function findPayroll(int $id): ?Payroll
    {
        return $this->payrollRepository->find($id);
    }

    /**
     * Get payrolls for a specific month
     */
    public function getPayrollsForMonth(int $month, int $year): Collection
    {
        return $this->payrollRepository->getPayrollsForMonth($month, $year);
    }
}
