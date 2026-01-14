<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Account;
use App\Models\Distribution;
use App\Models\DistributionLine;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Repositories\AccountRepository;
use App\Repositories\DistributionRepository;
use App\Repositories\ShareholderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DistributionService
{
    public function __construct(
        private readonly DistributionRepository $distributionRepository,
        private readonly ShareholderRepository $shareholderRepository,
        private readonly TransactionService $transactionService,
        private readonly AccountRepository $accountRepository
    ) {}

    /**
     * Create new distribution in DRAFT state with calculations
     */
    public function createDistribution(array $data): Distribution
    {
        // Validate equity totals 100%
        $equityTotal = $this->shareholderRepository->getTotalEquityPercentage();
        if ($equityTotal !== 100.0) {
            throw new InvalidArgumentException(
                "Cannot create distribution. Shareholder equity totals {$equityTotal}%, must be 100%."
            );
        }

        // Validate date range (only if period-based)
        if (isset($data['period_start']) && isset($data['period_end']) && $data['period_start'] > $data['period_end']) {
            throw new InvalidArgumentException('Period start date must be before end date');
        }

        return DB::transaction(function () use ($data) {
            // Generate distribution number
            $distributionNumber = $this->generateDistributionNumber();

            // Determine if period-based or manual amount
            if (isset($data['manual_amount_pkr'])) {
                // Manual amount approach
                $distribution = $this->distributionRepository->create([
                    'distribution_number' => $distributionNumber,
                    'status' => 'draft',
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth(),
                    'total_revenue_pkr' => 0,
                    'total_expenses_pkr' => 0,
                    'calculated_net_profit_pkr' => 0,
                    'adjusted_net_profit_pkr' => $data['manual_amount_pkr'],
                    'adjustment_reason' => 'Manual distribution amount',
                    'distributed_amount_pkr' => 0, // Not processed yet
                    'notes' => $data['notes'] ?? null,
                ]);
            } else {
                // Period-based calculation (existing logic)
                $calculations = $this->calculateProfitForPeriod(
                    $data['period_start'],
                    $data['period_end']
                );

                $distribution = $this->distributionRepository->create([
                    'distribution_number' => $distributionNumber,
                    'status' => 'draft',
                    'period_start' => $data['period_start'],
                    'period_end' => $data['period_end'],
                    'total_revenue_pkr' => $calculations['total_revenue'],
                    'total_expenses_pkr' => $calculations['total_expenses'],
                    'calculated_net_profit_pkr' => $calculations['net_profit'],
                    'adjusted_net_profit_pkr' => null, // No adjustment initially
                    'distributed_amount_pkr' => 0, // Not processed yet
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            // Create distribution lines for all active shareholders
            $this->createDistributionLines($distribution);

            // If action=process, immediately process
            if (isset($data['action']) && $data['action'] === 'process' && isset($data['account_id'])) {
                return $this->processDistribution($distribution->id, $data['account_id']);
            }

            return $distribution->load(['lines.shareholder']);
        });
    }

    /**
     * Calculate profit from transactions using reporting_amount_pkr
     */
    private function calculateProfitForPeriod(string $periodStart, string $periodEnd): array
    {
        // IMPORTANT: Use reporting_amount_pkr for multi-currency support
        $totalRevenue = Transaction::where('type', 'credit')
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->sum('reporting_amount_pkr');

        $totalExpenses = Transaction::where('type', 'debit')
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->sum('reporting_amount_pkr');

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
        ];
    }

    /**
     * Create distribution lines for all active shareholders
     */
    private function createDistributionLines(Distribution $distribution): void
    {
        $shareholders = $this->shareholderRepository->getAllActive();
        $netProfit = $distribution->final_net_profit;

        foreach ($shareholders as $shareholder) {
            $allocatedAmount = (int) round($netProfit * ((float) $shareholder->equity_percentage / 100));

            DistributionLine::create([
                'distribution_id' => $distribution->id,
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
                'transaction_id' => null, // Set when processed
            ]);
        }
    }

    /**
     * Update distribution (only if draft)
     */
    public function updateDistribution(int $id, array $data): Distribution
    {
        return DB::transaction(function () use ($id, $data) {
            $distribution = $this->distributionRepository->find($id);

            if (! $distribution) {
                throw new InvalidArgumentException('Distribution not found');
            }

            if ($distribution->status !== 'draft') {
                throw new InvalidArgumentException('Only draft distributions can be edited');
            }

            // Handle period changes - recalculate
            if (isset($data['period_start']) || isset($data['period_end'])) {
                $periodStart = $data['period_start'] ?? $distribution->period_start->format('Y-m-d');
                $periodEnd = $data['period_end'] ?? $distribution->period_end->format('Y-m-d');

                $calculations = $this->calculateProfitForPeriod($periodStart, $periodEnd);

                $data['total_revenue_pkr'] = $calculations['total_revenue'];
                $data['total_expenses_pkr'] = $calculations['total_expenses'];
                $data['calculated_net_profit_pkr'] = $calculations['net_profit'];

                // Recalculate distribution lines
                $distribution->lines()->delete();
                $updatedDistribution = $this->distributionRepository->update($id, $data);
                $this->createDistributionLines($updatedDistribution);

                return $updatedDistribution->fresh(['lines.shareholder']);
            }

            return $this->distributionRepository->update($id, $data);
        });
    }

    /**
     * Manually adjust net profit (with reason)
     */
    public function adjustNetProfit(int $id, int $adjustedAmountPkr, string $reason): Distribution
    {
        return DB::transaction(function () use ($id, $adjustedAmountPkr, $reason) {
            $distribution = $this->distributionRepository->find($id);

            if (! $distribution) {
                throw new InvalidArgumentException('Distribution not found');
            }

            if ($distribution->status !== 'draft') {
                throw new InvalidArgumentException('Cannot adjust processed distributions');
            }

            // Update distribution
            $distribution = $this->distributionRepository->update($id, [
                'adjusted_net_profit_pkr' => $adjustedAmountPkr,
                'adjustment_reason' => $reason,
            ]);

            // Recalculate distribution lines with new amount
            $distribution->lines()->delete();
            $this->createDistributionLines($distribution);

            return $distribution->fresh(['lines.shareholder']);
        });
    }

    /**
     * CRITICAL: Process distribution - creates withdrawal transactions for human partners
     */
    public function processDistribution(int $id, int $accountId): Distribution
    {
        return DB::transaction(function () use ($id, $accountId) {
            $distribution = $this->distributionRepository->find($id);

            if (! $distribution) {
                throw new InvalidArgumentException('Distribution not found');
            }

            if ($distribution->status !== 'draft') {
                throw new InvalidArgumentException('Distribution has already been processed');
            }

            // Validate account exists and is PKR
            $account = $this->accountRepository->find($accountId);
            if (! $account) {
                throw new InvalidArgumentException('Account not found');
            }

            if ($account->currency_code !== 'PKR') {
                throw new InvalidArgumentException('Distributions can only be paid from PKR accounts');
            }

            // Calculate total needed (excluding Office)
            $humanPartnerLines = $distribution->lines()
                ->whereHas('shareholder', fn ($q) => $q->where('is_office_reserve', false))
                ->get();

            $totalNeeded = $humanPartnerLines->sum('allocated_amount_pkr');

            // HARD BLOCK: Validate balance
            if ($account->current_balance < $totalNeeded) {
                throw new InvalidArgumentException(
                    'Insufficient balance. Need '.
                    CurrencyHelper::format($totalNeeded / 100, 'PKR').
                    ', have '.$account->formatted_balance
                );
            }

            // Create withdrawal transactions for human partners only
            $distributedAmount = 0;
            foreach ($distribution->lines as $line) {
                if ($line->shareholder->is_office_reserve) {
                    // Office share stays in company - no transaction
                    continue;
                }

                // Create withdrawal transaction
                $transaction = $this->transactionService->createTransaction([
                    'account_id' => $accountId,
                    'category_id' => $this->getPartnerDistributionCategoryId(),
                    'reference_type' => Distribution::class,
                    'reference_id' => $distribution->id,
                    'type' => 'debit',
                    'amount' => $line->allocated_amount_pkr / 100, // Service converts to minor
                    'description' => "Distribution: {$line->shareholder->name} - {$distribution->distribution_number}",
                    'date' => now()->format('Y-m-d'),
                ]);

                // Link transaction to distribution line
                $line->update(['transaction_id' => $transaction->id]);

                $distributedAmount += $line->allocated_amount_pkr;
            }

            // Update distribution status
            return $this->distributionRepository->update($id, [
                'status' => 'processed',
                'processed_at' => now(),
                'distributed_amount_pkr' => $distributedAmount,
            ]);
        });
    }

    /**
     * Generate distribution number
     */
    private function generateDistributionNumber(): string
    {
        $prefix = 'DIST';
        $year = now()->year;
        $lastDistribution = Distribution::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDistribution
            ? ((int) substr($lastDistribution->distribution_number, -3) + 1)
            : 1;

        return sprintf('%s-%d-%03d', $prefix, $year, $sequence);
    }

    // === QUERY METHODS ===

    public function getPaginatedDistributions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'period_start',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->distributionRepository->paginateDistributions(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function findDistribution(int $id): ?Distribution
    {
        return $this->distributionRepository->find($id);
    }

    public function deleteDistribution(int $id): bool
    {
        $distribution = $this->distributionRepository->find($id);

        if (! $distribution) {
            throw new InvalidArgumentException('Distribution not found');
        }

        if ($distribution->status !== 'draft') {
            throw new InvalidArgumentException('Cannot delete processed distributions');
        }

        return $this->distributionRepository->delete($id);
    }

    /**
     * Get or create "Partner Distributions" category
     */
    private function getPartnerDistributionCategoryId(): int
    {
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'Partner Distributions', 'type' => 'expense'],
            ['color' => 'purple']
        );

        return $category->id;
    }

    /**
     * Generate PDF statement for a specific shareholder
     */
    public function generatePartnerStatement(int $distributionId, int $shareholderId): \Illuminate\Http\Response
    {
        $distribution = $this->distributionRepository->find($distributionId);

        if (! $distribution) {
            throw new InvalidArgumentException('Distribution not found');
        }

        $line = $distribution->lines()
            ->with('shareholder')
            ->where('shareholder_id', $shareholderId)
            ->first();

        if (! $line) {
            throw new InvalidArgumentException('Shareholder not found in this distribution');
        }

        $shareholder = $line->shareholder;

        // Create buyer (partner in this case)
        $buyer = new \LaravelDaily\Invoices\Classes\Buyer([
            'name' => $shareholder->name,
            'custom_fields' => [
                'email' => $shareholder->email ?? 'N/A',
                'equity' => number_format((float) $shareholder->equity_percentage, 2).'%',
            ],
        ]);

        // Create statement items
        $items = [
            (new \LaravelDaily\Invoices\Classes\InvoiceItem)
                ->title('Share of Net Profit')
                ->description("For period: {$distribution->period_start->format('M d, Y')} - {$distribution->period_end->format('M d, Y')}")
                ->quantity(1)
                ->pricePerUnit($line->allocated_amount_pkr / 100)
                ->units('PKR'),
        ];

        // Generate PDF
        $pdf = \LaravelDaily\Invoices\Invoice::make()
            ->buyer($buyer)
            ->addItems($items)
            ->name($distribution->distribution_number)
            ->date($distribution->processed_at ?? now())
            ->dateFormat('M d, Y')
            ->currencySymbol('Rs')
            ->currencyCode('PKR')
            ->notes(
                'Revenue: '.CurrencyHelper::format($distribution->total_revenue_pkr / 100, 'PKR')."\n".
                'Expenses: '.CurrencyHelper::format($distribution->total_expenses_pkr / 100, 'PKR')."\n".
                'Net Profit: '.CurrencyHelper::format($distribution->final_net_profit / 100, 'PKR')."\n".
                "Your Share ({$shareholder->formatted_equity}): ".CurrencyHelper::format($line->allocated_amount_pkr / 100, 'PKR')
            )
            ->filename("{$distribution->distribution_number}-{$shareholder->name}");

        return $pdf->stream();
    }

    /**
     * Get distributable profit (undistributed profit available)
     */
    public function getDistributableProfit(): array
    {
        // Calculate total profit from all processed distributions
        // Use adjusted_net_profit_pkr if set, otherwise calculated_net_profit_pkr
        $totalProfit = Distribution::where('status', 'processed')
            ->selectRaw('COALESCE(SUM(COALESCE(adjusted_net_profit_pkr, calculated_net_profit_pkr)), 0) as total')
            ->value('total');

        // Calculate total distributed amount (what was actually paid out)
        $totalDistributed = Distribution::where('status', 'processed')
            ->sum('distributed_amount_pkr');

        $undistributed = $totalProfit - $totalDistributed;

        return [
            'amount_pkr' => $undistributed,
            'formatted_amount' => CurrencyHelper::format($undistributed / 100, 'PKR'),
        ];
    }

    /**
     * Calculate runway (how long company can operate on Office reserves)
     */
    public function calculateRunway(): array
    {
        // Get total Office Reserve allocations (retained earnings)
        $officeBalance = DB::table('distribution_lines')
            ->join('distributions', 'distribution_lines.distribution_id', '=', 'distributions.id')
            ->join('shareholders', 'distribution_lines.shareholder_id', '=', 'shareholders.id')
            ->where('shareholders.is_office_reserve', true)
            ->where('distributions.status', 'processed')
            ->sum('distribution_lines.allocated_amount_pkr');

        // Calculate average monthly expenses from last 6 months
        $sixMonthsAgo = now()->subMonths(6);
        $avgMonthlyExpenses = Transaction::where('type', 'debit')
            ->where('date', '>=', $sixMonthsAgo)
            ->avg(DB::raw('reporting_amount_pkr'));

        if (! $avgMonthlyExpenses || $avgMonthlyExpenses == 0) {
            return [
                'runway_months' => 0,
                'office_balance_pkr' => $officeBalance,
                'formatted_office_balance' => CurrencyHelper::format($officeBalance / 100, 'PKR'),
                'avg_monthly_expenses_pkr' => 0,
                'formatted_avg_monthly_expenses' => CurrencyHelper::format(0, 'PKR'),
            ];
        }

        $runwayMonths = $officeBalance / $avgMonthlyExpenses;

        return [
            'runway_months' => round($runwayMonths, 1),
            'office_balance_pkr' => $officeBalance,
            'formatted_office_balance' => CurrencyHelper::format($officeBalance / 100, 'PKR'),
            'avg_monthly_expenses_pkr' => $avgMonthlyExpenses,
            'formatted_avg_monthly_expenses' => CurrencyHelper::format($avgMonthlyExpenses / 100, 'PKR'),
        ];
    }
}
