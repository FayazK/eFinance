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
                    'base_salary' => $employee->base_salary, // Snapshot
                    'deposit_currency' => $employee->deposit_currency->value, // Snapshot deposit method
                    'bonus' => 0,
                    'deductions' => 0,
                    'net_payable' => $employee->base_salary,
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
     * Pay multiple payrolls in batch (supports mixed currencies)
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

            // Group payrolls by currency
            $pkrPayrolls = $payrolls->filter(fn ($p) => ($p->deposit_currency?->value ?? 'PKR') === 'PKR');
            $usdPayrolls = $payrolls->filter(fn ($p) => ($p->deposit_currency?->value ?? 'PKR') === 'USD');

            $payments = [];

            // Process PKR payrolls
            if ($pkrPayrolls->isNotEmpty()) {
                $pkrAccount = $this->accountRepository->find($data['pkr_account_id']);
                if (! $pkrAccount) {
                    throw new InvalidArgumentException('PKR account not found');
                }
                if ($pkrAccount->currency_code !== 'PKR') {
                    throw new InvalidArgumentException('PKR account must be a PKR currency account');
                }

                $pkrTotal = $pkrPayrolls->sum('net_payable');
                if ($pkrAccount->current_balance < $pkrTotal) {
                    throw new InvalidArgumentException('Insufficient PKR balance');
                }

                foreach ($pkrPayrolls as $payroll) {
                    $payments[] = $this->paySinglePayroll($payroll, [
                        'account_id' => $data['pkr_account_id'],
                        'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                    ]);
                }
            }

            // Process USD payrolls
            if ($usdPayrolls->isNotEmpty()) {
                $usdAccount = $this->accountRepository->find($data['usd_account_id']);
                if (! $usdAccount) {
                    throw new InvalidArgumentException('USD account not found');
                }
                if ($usdAccount->currency_code !== 'USD') {
                    throw new InvalidArgumentException('USD account must be a USD currency account');
                }

                $exchangeRate = (float) ($data['exchange_rate'] ?? 0);
                if ($exchangeRate <= 0) {
                    throw new InvalidArgumentException('Exchange rate is required for USD payrolls');
                }

                // Calculate total USD needed: PKR ÷ rate = USD (in minor units)
                $usdTotalPkr = $usdPayrolls->sum('net_payable');
                $usdNeeded = (int) round($usdTotalPkr / $exchangeRate);
                if ($usdAccount->current_balance < $usdNeeded) {
                    throw new InvalidArgumentException('Insufficient USD balance');
                }

                foreach ($usdPayrolls as $payroll) {
                    $payments[] = $this->paySinglePayrollUsd($payroll, [
                        'account_id' => $data['usd_account_id'],
                        'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                        'exchange_rate' => $exchangeRate,
                    ]);
                }
            }

            return $payments;
        });
    }

    /**
     * Pay a single payroll (PKR)
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
     * Pay a single payroll (USD) - stores exchange rate and calculates USD amount
     */
    private function paySinglePayrollUsd(Payroll $payroll, array $data): Payroll
    {
        $exchangeRate = (float) $data['exchange_rate'];
        // Calculate USD amount: PKR ÷ rate = USD
        $usdAmountMinor = (int) round($payroll->net_payable / $exchangeRate);

        // Create expense transaction (in USD)
        $transaction = $this->transactionService->createTransaction([
            'account_id' => $data['account_id'],
            'category_id' => $this->getSalariesCategoryId(),
            'reference_type' => Payroll::class,
            'reference_id' => $payroll->id,
            'type' => 'debit',
            'amount' => $usdAmountMinor / 100, // Convert to major units
            'description' => "Salary (USD): {$payroll->employee->name} - {$payroll->period_label}",
            'date' => $data['payment_date'] ?? now()->format('Y-m-d'),
        ]);

        // Update payroll with exchange rate
        $payroll = $this->payrollRepository->update($payroll->id, [
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transaction->id,
            'exchange_rate' => $exchangeRate,
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
