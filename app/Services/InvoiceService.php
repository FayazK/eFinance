<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\TransactionCategory;
use App\Repositories\AccountRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceService
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private TransactionService $transactionService,
        private AccountRepository $accountRepository
    ) {}

    /**
     * Create a new invoice with line items
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals from line items
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $itemAmount = (int) (($item['quantity'] * $item['unit_price']) * 100);
                $subtotal += $itemAmount;
            }

            $taxAmount = isset($data['tax_amount']) ? (int) ($data['tax_amount'] * 100) : 0;
            $totalAmount = $subtotal + $taxAmount;

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create invoice
            $invoice = $this->invoiceRepository->create([
                'invoice_number' => $invoiceNumber,
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'] ?? null,
                'currency_code' => $data['currency_code'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'balance_due' => $totalAmount,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'client_notes' => $data['client_notes'] ?? null,
                'status' => 'draft',
            ]);

            // Create line items
            foreach ($data['items'] as $index => $item) {
                $unitPrice = (int) ($item['unit_price'] * 100);
                $amount = (int) (($item['quantity'] * $item['unit_price']) * 100);

                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'unit',
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'sort_order' => $index,
                ]);
            }

            return $invoice->load(['items', 'client', 'project']);
        });
    }

    /**
     * Update an existing invoice (only if status is draft)
     */
    public function updateInvoice(int $invoiceId, array $data): Invoice
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = $this->invoiceRepository->find($invoiceId);

            if (! $invoice) {
                throw new InvalidArgumentException('Invoice not found');
            }

            if ($invoice->status !== 'draft') {
                throw new InvalidArgumentException('Only draft invoices can be edited');
            }

            // Recalculate totals if items provided
            if (isset($data['items'])) {
                $subtotal = 0;
                foreach ($data['items'] as $item) {
                    $itemAmount = (int) (($item['quantity'] * $item['unit_price']) * 100);
                    $subtotal += $itemAmount;
                }

                $taxAmount = isset($data['tax_amount']) ? (int) ($data['tax_amount'] * 100) : 0;
                $totalAmount = $subtotal + $taxAmount;

                $data['subtotal'] = $subtotal;
                $data['tax_amount'] = $taxAmount;
                $data['total_amount'] = $totalAmount;
                $data['balance_due'] = $totalAmount;

                // Delete old items and create new ones
                $invoice->items()->delete();
                foreach ($data['items'] as $index => $item) {
                    $unitPrice = (int) ($item['unit_price'] * 100);
                    $amount = (int) (($item['quantity'] * $item['unit_price']) * 100);

                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'] ?? 'unit',
                        'unit_price' => $unitPrice,
                        'amount' => $amount,
                        'sort_order' => $index,
                    ]);
                }

                unset($data['items']);
            }

            return $this->invoiceRepository->update($invoiceId, $data);
        });
    }

    /**
     * Change invoice status with validation
     */
    public function changeStatus(int $invoiceId, string $newStatus): Invoice
    {
        $invoice = $this->invoiceRepository->find($invoiceId);

        if (! $invoice) {
            throw new InvalidArgumentException('Invoice not found');
        }

        $this->validateStatusTransition($invoice->status, $newStatus);

        $updates = ['status' => $newStatus];

        // Set timestamps for specific status changes
        if ($newStatus === 'sent') {
            $updates['sent_at'] = now();
        } elseif ($newStatus === 'paid') {
            $updates['paid_at'] = now();
        } elseif ($newStatus === 'void') {
            $updates['voided_at'] = now();
        }

        return $this->invoiceRepository->update($invoiceId, $updates);
    }

    /**
     * CRITICAL: Record payment with "gross-up" workflow
     *
     * This handles the scenario where:
     * - Invoice total: $5,000 USD
     * - Amount received: $4,750 USD (after bank fees)
     * - Fee: $250 USD
     *
     * Creates TWO transactions:
     * 1. Income (credit): +$5,000 to record full revenue
     * 2. Expense (debit): -$250 as "Bank Charges & Fees"
     *
     * Result: Account balance = $4,750 (correct)
     *         Revenue = $5,000 (accurate)
     *         Expenses = $250 (transparent)
     */
    public function recordPayment(int $invoiceId, array $paymentData): InvoicePayment
    {
        return DB::transaction(function () use ($invoiceId, $paymentData) {
            $invoice = $this->invoiceRepository->find($invoiceId);

            if (! $invoice) {
                throw new InvalidArgumentException('Invoice not found');
            }

            // Validation
            if (! $invoice->is_payable) {
                throw new InvalidArgumentException(
                    "Invoice status '{$invoice->status}' does not allow payments"
                );
            }

            $account = $this->accountRepository->find($paymentData['account_id']);

            if (! $account) {
                throw new InvalidArgumentException('Account not found');
            }

            // Currency validation
            if ($invoice->currency_code !== $account->currency_code) {
                throw new InvalidArgumentException(
                    "Invoice currency ({$invoice->currency_code}) must match account currency ({$account->currency_code})"
                );
            }

            // Convert to minor units
            $paymentAmount = (int) ($paymentData['payment_amount'] * 100);
            $amountReceived = (int) ($paymentData['amount_received'] * 100);

            // Calculate fee
            $feeAmount = $paymentAmount - $amountReceived;

            if ($feeAmount < 0) {
                throw new InvalidArgumentException(
                    'Amount received cannot exceed payment amount'
                );
            }

            // Validate payment doesn't exceed balance
            if ($paymentAmount > $invoice->balance_due) {
                throw new InvalidArgumentException(
                    'Payment amount exceeds invoice balance'
                );
            }

            // === STEP 1: Record FULL revenue as income transaction ===
            $incomeTransaction = $this->transactionService->createTransaction([
                'account_id' => $account->id,
                'category_id' => $this->getIncomeCategoryId(),
                'reference_type' => Invoice::class,
                'reference_id' => $invoice->id,
                'type' => 'credit',
                'amount' => $paymentAmount / 100, // Service converts to minor units
                'description' => "Payment for {$invoice->invoice_number}",
                'date' => $paymentData['payment_date'],
            ]);

            // === STEP 2: Record fee as expense (if applicable) ===
            $feeTransactionId = null;
            if ($feeAmount > 0) {
                $feeTransaction = $this->transactionService->createTransaction([
                    'account_id' => $account->id,
                    'category_id' => $this->getBankChargesCategoryId(),
                    'reference_type' => Invoice::class,
                    'reference_id' => $invoice->id,
                    'type' => 'debit',
                    'amount' => $feeAmount / 100, // Service converts to minor units
                    'description' => "Bank charges for {$invoice->invoice_number}",
                    'date' => $paymentData['payment_date'],
                ]);
                $feeTransactionId = $feeTransaction->id;
            }

            // === STEP 3: Create payment record ===
            $invoicePayment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'account_id' => $account->id,
                'income_transaction_id' => $incomeTransaction->id,
                'fee_transaction_id' => $feeTransactionId,
                'payment_amount' => $paymentAmount,
                'amount_received' => $amountReceived,
                'fee_amount' => $feeAmount,
                'payment_date' => $paymentData['payment_date'],
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // === STEP 4: Update invoice balance and status ===
            $newAmountPaid = $invoice->amount_paid + $paymentAmount;
            $newBalance = $invoice->total_amount - $newAmountPaid;

            $newStatus = $invoice->status;
            if ($newBalance === 0) {
                $newStatus = 'paid';
            } elseif ($newAmountPaid > 0 && $newBalance > 0) {
                $newStatus = 'partial';
            }

            $this->invoiceRepository->update($invoice->id, [
                'amount_paid' => $newAmountPaid,
                'balance_due' => $newBalance,
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : null,
            ]);

            return $invoicePayment->load([
                'invoice',
                'account',
                'incomeTransaction',
                'feeTransaction',
            ]);
        });
    }

    /**
     * Void an invoice (reverses all transactions)
     *
     * @param  int  $invoiceId  The invoice to void
     * @param  string|null  $voidReason  Required reason for voiding (for audit trail)
     */
    public function voidInvoice(int $invoiceId, ?string $voidReason = null): Invoice
    {
        return DB::transaction(function () use ($invoiceId, $voidReason) {
            $invoice = $this->invoiceRepository->find($invoiceId);

            if (! $invoice) {
                throw new InvalidArgumentException('Invoice not found');
            }

            if ($invoice->status === 'void') {
                throw new InvalidArgumentException('Invoice is already voided');
            }

            // Reverse all payment transactions and mark payments as voided
            foreach ($invoice->payments as $payment) {
                // Skip already voided payments
                if ($payment->is_voided) {
                    continue;
                }

                // Reverse income transaction (debit to reduce balance)
                $this->transactionService->createTransaction([
                    'account_id' => $payment->account_id,
                    'type' => 'debit',
                    'amount' => $payment->payment_amount / 100,
                    'description' => "Void reversal: {$invoice->invoice_number}",
                    'date' => now()->format('Y-m-d'),
                ]);

                // Reverse fee transaction if exists (credit to restore the fee amount)
                if ($payment->fee_transaction_id) {
                    $this->transactionService->createTransaction([
                        'account_id' => $payment->account_id,
                        'type' => 'credit',
                        'amount' => $payment->fee_amount / 100,
                        'description' => "Void reversal (fee): {$invoice->invoice_number}",
                        'date' => now()->format('Y-m-d'),
                    ]);
                }

                // Mark payment as voided for audit trail
                $payment->update(['voided_at' => now()]);
            }

            // Update invoice: void status, reset amounts, store reason
            return $this->invoiceRepository->update($invoiceId, [
                'status' => 'void',
                'voided_at' => now(),
                'void_reason' => $voidReason,
                'amount_paid' => 0,
                'balance_due' => $invoice->total_amount,
                'paid_at' => null,
            ]);
        });
    }

    /**
     * Update invoice due date (allowed for draft, sent, partial, overdue statuses)
     */
    public function updateDueDate(int $invoiceId, string $dueDate): Invoice
    {
        $invoice = $this->invoiceRepository->find($invoiceId);

        if (! $invoice) {
            throw new InvalidArgumentException('Invoice not found');
        }

        // Only unpaid/non-void invoices can have their due date edited
        if (in_array($invoice->status, ['paid', 'void'])) {
            throw new InvalidArgumentException('Cannot edit due date of paid or voided invoices');
        }

        return $this->invoiceRepository->update($invoiceId, [
            'due_date' => $dueDate,
        ]);
    }

    /**
     * Mark overdue invoices automatically
     */
    public function markOverdueInvoices(): int
    {
        return $this->invoiceRepository->markOverdueInvoices();
    }

    // === QUERY METHODS ===

    public function getPaginatedInvoices(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'issue_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->invoiceRepository->paginateInvoices(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function findInvoice(int $id): ?Invoice
    {
        return $this->invoiceRepository->find($id);
    }

    public function deleteInvoice(int $id): bool
    {
        return $this->invoiceRepository->delete($id);
    }

    public function getClientInvoices(int $clientId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->invoiceRepository->getClientInvoices($clientId, $perPage);
    }

    public function getTotalReceivables(): int
    {
        return $this->invoiceRepository->getTotalReceivables();
    }

    // === PRIVATE HELPERS ===

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->year;
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -3) + 1) : 1;

        return sprintf('%s-%d-%03d', $prefix, $year, $sequence);
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $validTransitions = [
            'draft' => ['sent', 'void'],
            'sent' => ['partial', 'paid', 'overdue', 'void'],
            'partial' => ['paid', 'overdue', 'void'],
            'overdue' => ['partial', 'paid', 'void'],
            'paid' => [], // Paid invoices cannot be changed
            'void' => [], // Cannot transition from void
        ];

        if (! in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            throw new InvalidArgumentException(
                "Invalid status transition from '{$currentStatus}' to '{$newStatus}'"
            );
        }
    }

    private function getIncomeCategoryId(): int
    {
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'Invoice Payment', 'type' => 'income'],
            ['color' => 'green']
        );

        return $category->id;
    }

    private function getBankChargesCategoryId(): int
    {
        $category = TransactionCategory::firstOrCreate(
            ['name' => 'Bank Charges & Fees', 'type' => 'expense'],
            ['color' => 'gray']
        );

        return $category->id;
    }
}
