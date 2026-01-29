<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AccountRepository $accountRepository,
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Get financial overview with account balances by currency
     */
    public function getFinancialOverview(): array
    {
        $accountsByCurrency = Account::where('is_active', true)
            ->select('currency_code', DB::raw('SUM(current_balance) as total'))
            ->groupBy('currency_code')
            ->get()
            ->map(function ($item) {
                return [
                    'currency_code' => $item->currency_code,
                    'total_balance' => (int) $item->total,
                    'formatted_balance' => CurrencyHelper::format($item->total / 100, $item->currency_code),
                ];
            })
            ->toArray();

        $totalActiveAccounts = Account::where('is_active', true)->count();

        return [
            'accounts_by_currency' => $accountsByCurrency,
            'total_active_accounts' => $totalActiveAccounts,
        ];
    }

    /**
     * Get revenue metrics from invoices
     */
    public function getRevenueMetrics(): array
    {
        // Get total receivables
        $totalReceivables = $this->invoiceRepository->getTotalReceivables();

        // Get invoice counts by status
        $invoiceCounts = Invoice::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Default counts to 0 if no invoices exist
        $statusDefaults = [
            'draft' => 0,
            'sent' => 0,
            'partial' => 0,
            'paid' => 0,
            'overdue' => 0,
        ];

        $invoiceCounts = array_merge($statusDefaults, $invoiceCounts);

        // Get overdue count
        $overdueCount = Invoice::where('status', 'overdue')->count();

        // Calculate average invoice value
        $totalInvoices = Invoice::count();
        $totalInvoiceAmount = Invoice::sum('total_amount');
        $averageInvoiceValue = $totalInvoices > 0 ? (int) ($totalInvoiceAmount / $totalInvoices) : 0;

        return [
            'total_receivables' => $totalReceivables,
            'formatted_receivables' => CurrencyHelper::format($totalReceivables / 100, 'PKR'),
            'invoice_counts' => $invoiceCounts,
            'overdue_count' => $overdueCount,
            'average_invoice_value' => $averageInvoiceValue,
            'formatted_average' => CurrencyHelper::format($averageInvoiceValue / 100, 'PKR'),
        ];
    }

    /**
     * Get payroll summary
     */
    public function getPayrollSummary(): array
    {
        // Active employee count
        $activeEmployees = Employee::where('status', 'active')->count();

        // Last month's payroll expense
        $lastMonth = now()->subMonth();
        $lastMonthExpense = Payroll::where('year', $lastMonth->year)
            ->where('month', $lastMonth->month)
            ->where('status', 'paid')
            ->sum('net_payable');

        // Pending payrolls
        $pendingPayrolls = Payroll::where('status', 'pending')->count();

        // Average salary (from active employees)
        $averageSalary = $activeEmployees > 0
            ? Employee::where('status', 'active')->avg('base_salary')
            : 0;

        return [
            'active_employees' => $activeEmployees,
            'last_month_expense' => (int) $lastMonthExpense,
            'formatted_last_month' => CurrencyHelper::format($lastMonthExpense / 100, 'PKR'),
            'pending_payrolls' => $pendingPayrolls,
            'average_salary' => (int) $averageSalary,
            'formatted_average_salary' => CurrencyHelper::format($averageSalary / 100, 'PKR'),
        ];
    }

    /**
     * Get cash flow trend for specified months, grouped by currency
     */
    public function getCashFlowTrend(int $months = 6): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        // Get transaction data grouped by month, type, and currency
        $transactionData = Transaction::select(
            DB::raw('YEAR(transactions.date) as year'),
            DB::raw('MONTH(transactions.date) as month'),
            'transactions.type',
            'accounts.currency_code',
            DB::raw('SUM(transactions.amount) as total')
        )
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('transactions.date', '>=', $startDate)
            ->groupBy('year', 'month', 'transactions.type', 'accounts.currency_code')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Initialize months array for each currency
        $currencies = ['PKR', 'USD'];
        $result = [];

        foreach ($currencies as $currency) {
            $monthsData = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->year.'-'.$date->month;
                $monthsData[$key] = [
                    'label' => $date->format('M Y'),
                    'year' => $date->year,
                    'month' => $date->month,
                    'income' => 0,
                    'expenses' => 0,
                    'net' => 0,
                ];
            }

            // Fill in transaction data for this currency
            foreach ($transactionData as $transaction) {
                if ($transaction->currency_code !== $currency) {
                    continue;
                }

                $key = $transaction->year.'-'.$transaction->month;
                if (isset($monthsData[$key])) {
                    if ($transaction->type === 'credit') {
                        $monthsData[$key]['income'] = (int) $transaction->total;
                    } else {
                        $monthsData[$key]['expenses'] = (int) $transaction->total;
                    }
                }
            }

            // Calculate net for each month
            foreach ($monthsData as &$month) {
                $month['net'] = $month['income'] - $month['expenses'];
            }

            $result[$currency] = [
                'currency_code' => $currency,
                'months' => array_values($monthsData),
            ];
        }

        return [
            'currencies' => array_values($result),
        ];
    }

    /**
     * Get invoice status breakdown for pie chart
     */
    public function getInvoiceStatusBreakdown(): array
    {
        $statuses = Invoice::select(
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'total_amount' => (int) $item->total,
                    'formatted_amount' => CurrencyHelper::format($item->total / 100, 'PKR'),
                ];
            })
            ->toArray();

        return [
            'statuses' => $statuses,
        ];
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10): Collection
    {
        return $this->transactionRepository->getRecentTransactions($limit);
    }
}
