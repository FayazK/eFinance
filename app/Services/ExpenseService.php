<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Repositories\ExpenseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ExpenseService
{
    public function __construct(
        private ExpenseRepository $expenseRepository,
        private TransactionService $transactionService
    ) {}

    /**
     * Create a draft expense (no transaction created yet)
     */
    public function createDraftExpense(array $data): Expense
    {
        // Convert amount to minor units and calculate PKR reporting amount
        $amountInMinor = (int) ($data['amount'] * 100);
        $exchangeRate = isset($data['exchange_rate']) ? (float) $data['exchange_rate'] : null;
        $reportingAmountPkr = $this->calculateReportingAmountPkr(
            $amountInMinor,
            $data['currency_code'],
            $exchangeRate
        );

        // Create expense record as draft
        return $this->expenseRepository->create([
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'amount' => $amountInMinor,
            'currency_code' => $data['currency_code'],
            'vendor' => $data['vendor'] ?? null,
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date'] ?? now()->format('Y-m-d'),
            'exchange_rate' => $data['exchange_rate'] ?? null,
            'reporting_amount_pkr' => $reportingAmountPkr,
            'status' => 'draft',
            'is_recurring' => false,
        ]);
    }

    /**
     * Create and process a one-time expense (Quick Entry or International)
     */
    public function createAndProcessExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            // Create draft expense first
            $expense = $this->createDraftExpense($data);

            // Process the expense (create transaction)
            return $this->processExpense($expense->id);
        });
    }

    /**
     * Update a draft expense
     */
    public function updateExpense(int $expenseId, array $data): Expense
    {
        $expense = $this->expenseRepository->find($expenseId);

        if (! $expense) {
            throw new InvalidArgumentException("Expense {$expenseId} not found");
        }

        if ($expense->status !== 'draft') {
            throw new InvalidArgumentException('Only draft expenses can be edited');
        }

        // Convert amount to minor units and recalculate PKR reporting amount
        $amountInMinor = (int) ($data['amount'] * 100);
        $exchangeRate = isset($data['exchange_rate']) ? (float) $data['exchange_rate'] : null;
        $reportingAmountPkr = $this->calculateReportingAmountPkr(
            $amountInMinor,
            $data['currency_code'],
            $exchangeRate
        );

        return $this->expenseRepository->update($expenseId, [
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'amount' => $amountInMinor,
            'currency_code' => $data['currency_code'],
            'vendor' => $data['vendor'] ?? null,
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date'],
            'exchange_rate' => $data['exchange_rate'] ?? null,
            'reporting_amount_pkr' => $reportingAmountPkr,
        ]);
    }

    /**
     * Create a recurring expense template (no transaction yet)
     */
    public function createRecurringExpense(array $data): Expense
    {
        // Convert amount to minor units
        $amountInMinor = (int) ($data['amount'] * 100);

        // Calculate next occurrence date
        $startDate = Carbon::parse($data['recurrence_start_date'] ?? now());
        $nextOccurrence = $this->calculateNextOccurrenceFromDate(
            $startDate,
            $data['recurrence_frequency'],
            (int) ($data['recurrence_interval'] ?? 1)
        );

        return $this->expenseRepository->create([
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'amount' => $amountInMinor,
            'currency_code' => $data['currency_code'],
            'vendor' => $data['vendor'] ?? null,
            'description' => $data['description'] ?? null,
            'expense_date' => $startDate->format('Y-m-d'),
            'is_recurring' => true,
            'is_active' => true,
            'recurrence_frequency' => $data['recurrence_frequency'],
            'recurrence_interval' => (int) ($data['recurrence_interval'] ?? 1),
            'recurrence_start_date' => $startDate->format('Y-m-d'),
            'recurrence_end_date' => $data['recurrence_end_date'] ?? null,
            'next_occurrence_date' => $nextOccurrence->format('Y-m-d'),
            'status' => 'draft',
        ]);
    }

    /**
     * Process an expense by creating a transaction
     */
    public function processExpense(int $expenseId, ?array $overrides = null): Expense
    {
        return DB::transaction(function () use ($expenseId, $overrides) {
            $expense = $this->expenseRepository->find($expenseId);

            if (! $expense) {
                throw new InvalidArgumentException("Expense {$expenseId} not found");
            }

            if ($expense->status === 'processed') {
                throw new InvalidArgumentException("Expense {$expenseId} is already processed");
            }

            // Get or create expense category for transactions
            $transactionCategoryId = $this->getExpenseCategoryId();

            // Create transaction
            $transaction = $this->transactionService->createTransaction([
                'account_id' => $expense->account_id,
                'category_id' => $transactionCategoryId,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'type' => 'debit',
                'amount' => $expense->amount / 100, // Convert back to major units for service
                'reporting_amount_pkr' => $expense->reporting_amount_pkr ? $expense->reporting_amount_pkr / 100 : null,
                'reporting_exchange_rate' => $expense->exchange_rate,
                'description' => $this->buildTransactionDescription($expense),
                'date' => $overrides['expense_date'] ?? $expense->expense_date->format('Y-m-d'),
            ]);

            // Update expense with transaction link
            $expense = $this->expenseRepository->update($expense->id, [
                'transaction_id' => $transaction->id,
                'status' => 'processed',
            ]);

            // Log the expense processing
            activity()
                ->performedOn($expense)
                ->causedBy(auth()->user())
                ->withProperties([
                    'amount' => $expense->amount / 100,
                    'currency' => $expense->currency_code,
                    'vendor' => $expense->vendor,
                    'transaction_id' => $transaction->id,
                ])
                ->log('Expense processed');

            return $expense;
        });
    }

    /**
     * Process all due recurring expenses
     */
    public function processDueRecurringExpenses(): array
    {
        $dueExpenses = $this->expenseRepository->getDueRecurringExpenses();
        $processed = 0;
        $failed = [];

        foreach ($dueExpenses as $template) {
            try {
                DB::transaction(function () use ($template) {
                    // Create a new expense from the template
                    $newExpense = $this->expenseRepository->create([
                        'account_id' => $template->account_id,
                        'category_id' => $template->category_id,
                        'amount' => $template->amount,
                        'currency_code' => $template->currency_code,
                        'vendor' => $template->vendor,
                        'description' => ($template->description ?? '').' (Recurring)',
                        'expense_date' => $template->next_occurrence_date,
                        'status' => 'draft',
                        'is_recurring' => false,
                    ]);

                    // Process the new expense
                    $this->processExpense($newExpense->id, [
                        'expense_date' => $template->next_occurrence_date,
                    ]);

                    // Update template with next occurrence
                    $nextDate = $this->calculateNextOccurrence($template);

                    $this->expenseRepository->update($template->id, [
                        'last_processed_date' => now()->format('Y-m-d'),
                        'next_occurrence_date' => $nextDate?->format('Y-m-d'),
                        'is_active' => $nextDate !== null,
                    ]);
                });

                $processed++;
            } catch (\Exception $e) {
                $failed[] = [
                    'id' => $template->id,
                    'vendor' => $template->vendor,
                    'error' => $e->getMessage(),
                ];
                Log::error("Failed to process recurring expense {$template->id}: {$e->getMessage()}");
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
        ];
    }

    /**
     * Cancel an expense
     */
    public function cancelExpense(int $expenseId): Expense
    {
        return $this->expenseRepository->update($expenseId, [
            'status' => 'cancelled',
            'is_active' => false,
        ]);
    }

    /**
     * Void a processed expense
     *
     * Creates a credit reversal transaction to restore the money back to the account.
     *
     * @param  int  $expenseId  The expense to void
     * @param  string|null  $voidReason  Required reason for voiding (for audit trail)
     */
    public function voidExpense(int $expenseId, ?string $voidReason = null): Expense
    {
        return DB::transaction(function () use ($expenseId, $voidReason) {
            $expense = $this->expenseRepository->find($expenseId);

            if (! $expense) {
                throw new InvalidArgumentException("Expense {$expenseId} not found");
            }

            if ($expense->status === 'voided') {
                throw new InvalidArgumentException('Expense is already voided');
            }

            if ($expense->status !== 'processed') {
                throw new InvalidArgumentException('Only processed expenses can be voided');
            }

            // Create credit reversal transaction (restore money to account)
            $reversalTransaction = $this->transactionService->createTransaction([
                'account_id' => $expense->account_id,
                'category_id' => $this->getExpenseCategoryId(),
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'type' => 'credit',
                'amount' => $expense->amount / 100, // Convert to major units for service
                'description' => "Void reversal: Expense #{$expense->id} - {$expense->vendor}",
                'date' => now()->format('Y-m-d'),
            ]);

            // Update expense: void status, store reason and timestamp
            $expense = $this->expenseRepository->update($expenseId, [
                'status' => 'voided',
                'voided_at' => now(),
                'void_reason' => $voidReason,
            ]);

            // Log the void action with business context
            activity()
                ->performedOn($expense)
                ->causedBy(auth()->user())
                ->withProperties([
                    'void_reason' => $voidReason,
                    'amount' => $expense->amount / 100,
                    'currency' => $expense->currency_code,
                    'vendor' => $expense->vendor,
                    'original_transaction_id' => $expense->transaction_id,
                    'reversal_transaction_id' => $reversalTransaction->id,
                ])
                ->log('Expense voided');

            return $expense;
        });
    }

    /**
     * Delete a draft expense (hard delete)
     */
    public function deleteExpense(int $expenseId): bool
    {
        $expense = $this->expenseRepository->find($expenseId);

        if (! $expense) {
            throw new InvalidArgumentException("Expense {$expenseId} not found");
        }

        if ($expense->status !== 'draft') {
            throw new InvalidArgumentException('Only draft expenses can be deleted');
        }

        // Delete associated media (receipts)
        $expense->clearMediaCollection('receipts');

        return $this->expenseRepository->delete($expenseId);
    }

    /**
     * Get paginated expenses
     */
    public function getPaginatedExpenses(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'expense_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->expenseRepository->paginateExpenses(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    /**
     * Get paginated recurring expenses
     */
    public function getPaginatedRecurringExpenses(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->expenseRepository->paginateRecurringExpenses($perPage, $search);
    }

    /**
     * Get office fund metrics for dashboard
     */
    public function getOfficeFundMetrics(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end) ?: 1;

        $totalExpenses = $this->expenseRepository->getTotalExpensesByCategory($startDate, $endDate)
            ->sum('total');

        $expensesByCategory = $this->expenseRepository->getTotalExpensesByCategory($startDate, $endDate)
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'Uncategorized',
                    'total' => $item->total,
                    'formatted_total' => number_format($item->total / 100, 2),
                ];
            });

        return [
            'total_expenses' => $totalExpenses,
            'daily_burn_rate' => (int) ($totalExpenses / $days),
            'monthly_burn_rate' => (int) (($totalExpenses / $days) * 30),
            'expenses_by_category' => $expensesByCategory,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    /**
     * Find an expense by ID
     */
    public function findExpense(int $id): ?Expense
    {
        return $this->expenseRepository->find($id);
    }

    /**
     * Get last used exchange rate for a currency
     */
    public function getLastExchangeRate(string $fromCurrency): ?float
    {
        // Try to get from recent transactions
        $lastTransaction = Transaction::whereHas('account', fn ($q) => $q->where('currency_code', $fromCurrency))
            ->where('reporting_exchange_rate', '>', 0)
            ->latest('date')
            ->first();

        if ($lastTransaction) {
            return (float) $lastTransaction->reporting_exchange_rate;
        }

        // Fall back to config default
        return config("currency.default_rates.{$fromCurrency}");
    }

    /**
     * Private helper methods
     */
    private function calculateReportingAmountPkr(int $amount, string $currency, ?float $rate): int
    {
        if ($currency === 'PKR') {
            return $amount;
        }

        if (! $rate) {
            $rate = $this->getLastExchangeRate($currency) ?? 1.0;
        }

        return (int) ($amount * $rate);
    }

    private function calculateNextOccurrence(Expense $expense): ?Carbon
    {
        if (! $expense->is_recurring || ! $expense->is_active) {
            return null;
        }

        $current = $expense->next_occurrence_date;

        return $this->calculateNextOccurrenceFromDate(
            $current,
            $expense->recurrence_frequency,
            $expense->recurrence_interval
        );
    }

    private function calculateNextOccurrenceFromDate(
        Carbon $current,
        string $frequency,
        int $interval
    ): Carbon {
        $next = match ($frequency) {
            'monthly' => $current->copy()->addMonths($interval),
            'quarterly' => $current->copy()->addMonths(3 * $interval),
            'yearly' => $current->copy()->addYears($interval),
            default => $current->copy()->addMonth(),
        };

        return $next;
    }

    private function buildTransactionDescription(Expense $expense): string
    {
        $parts = ['Expense'];

        if ($expense->vendor) {
            $parts[] = "- {$expense->vendor}";
        }

        if ($expense->description) {
            $parts[] = "- {$expense->description}";
        }

        return implode(' ', $parts);
    }

    private function getExpenseCategoryId(): ?int
    {
        // Get or create a general "Expenses" transaction category
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'General Expenses', 'type' => 'expense'],
            ['color' => '#ef4444']
        );

        return $category->id;
    }
}
