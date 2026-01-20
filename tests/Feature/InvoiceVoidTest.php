<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed world package data
    $this->artisan('db:seed', ['--class' => 'WorldSeeder']);
});

describe('Invoice Void with Reason', function () {
    test('void reason is required when voiding an invoice', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => '',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['void_reason']);
    });

    test('void reason must not exceed 1000 characters', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => str_repeat('a', 1001),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['void_reason']);
    });

    test('user can void unpaid invoice with valid reason', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Client cancelled the order',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'void',
            'void_reason' => 'Client cancelled the order',
        ]);
        expect($invoice->fresh()->voided_at)->not->toBeNull();
    });
});

describe('Voiding Paid Invoices', function () {
    test('can void paid invoice with sufficient account balance', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 1000000, // $10,000 balance
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        // Create transactions first
        $incomeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500000,
        ]);
        $feeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 25000,
        ]);

        // Create payment record with transaction IDs
        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $incomeTransaction->id,
            'fee_transaction_id' => $feeTransaction->id,
            'payment_amount' => 500000,
            'amount_received' => 475000, // $4,750 after $250 fee
            'fee_amount' => 25000,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Client requested refund',
        ]);

        $response->assertOk();

        // Verify invoice is voided and amounts reset
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'void',
            'void_reason' => 'Client requested refund',
            'amount_paid' => 0,
            'balance_due' => 500000,
        ]);

        // Verify payment is marked as voided
        $payment->refresh();
        expect($payment->is_voided)->toBeTrue();
        expect($payment->voided_at)->not->toBeNull();

        // Verify reversal transactions were created
        // Debit to reverse income
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 500000,
        ]);

        // Credit to reverse fee
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 25000,
        ]);

        // Verify account balance is restored
        // Original: $10,000, Net reversal: -$4,750
        expect($account->fresh()->current_balance)->toBe(525000); // $5,250
    });

    test('cannot void paid invoice with insufficient account balance', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // Only $1,000 balance
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        // Create transaction first
        $incomeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500000,
        ]);
        $feeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 25000,
        ]);

        // Create payment record - needs $4,750 (net) to reverse
        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $incomeTransaction->id,
            'fee_transaction_id' => $feeTransaction->id,
            'payment_amount' => 500000,
            'amount_received' => 475000, // $4,750 net received
            'fee_amount' => 25000,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Client requested refund',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['void_reason']);

        // Verify invoice was not voided
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);

        // Verify payment was not marked as voided
        expect($payment->fresh()->is_voided)->toBeFalse();
    });

    test('can void partially paid invoice', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 500000, // $5,000 balance
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'amount_paid' => 300000, // $3,000 paid
            'balance_due' => 200000, // $2,000 remaining
            'status' => 'partial',
        ]);

        // Create transaction first
        $incomeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 300000,
        ]);

        // Create partial payment
        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $incomeTransaction->id,
            'fee_transaction_id' => null,
            'payment_amount' => 300000,
            'amount_received' => 300000, // No fee
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Project cancelled',
        ]);

        $response->assertOk();

        // Verify invoice is voided and amounts reset
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'void',
            'amount_paid' => 0,
            'balance_due' => 500000, // Reset to total
        ]);

        // Verify payment marked as voided
        expect($payment->fresh()->is_voided)->toBeTrue();

        // Verify account balance reduced
        expect($account->fresh()->current_balance)->toBe(200000); // $5,000 - $3,000 reversal = $2,000
    });

    test('voiding invoice with multiple payments reverses all payments', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 1000000, // $10,000 balance
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        // Create transactions first
        $tx1 = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 200000,
        ]);

        $tx2 = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 300000,
        ]);
        $tx3 = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 15000,
        ]);

        // Create two partial payments
        $payment1 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $tx1->id,
            'fee_transaction_id' => null,
            'payment_amount' => 200000, // $2,000
            'amount_received' => 200000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        $payment2 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $tx2->id,
            'fee_transaction_id' => $tx3->id,
            'payment_amount' => 300000, // $3,000
            'amount_received' => 285000, // $2,850 after $150 fee
            'fee_amount' => 15000,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Customer dispute resolved',
        ]);

        $response->assertOk();

        // Verify both payments are marked as voided
        expect($payment1->fresh()->is_voided)->toBeTrue();
        expect($payment2->fresh()->is_voided)->toBeTrue();

        // Verify all reversal transactions created
        // Payment 1 reversal: -$2,000 (debit)
        // Payment 2 reversal: -$3,000 (debit) and +$150 (credit for fee)
        // Net reversal: $2,000 + $2,850 = $4,850
        expect($account->fresh()->current_balance)->toBe(515000); // $10,000 - $4,850 = $5,150
    });
});

describe('Already Voided Invoice', function () {
    test('cannot void an already voided invoice', function () {
        $invoice = Invoice::factory()->create([
            'status' => 'void',
            'voided_at' => now(),
            'void_reason' => 'Already voided',
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Trying to void again',
        ]);

        $response->assertUnprocessable();
    });
});

describe('Voiding Invoices with Multiple Accounts', function () {
    test('can void invoice with payments from multiple accounts', function () {
        $account1 = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 500000, // $5,000
        ]);

        $account2 = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 500000, // $5,000
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        // Create transactions
        $tx1 = Transaction::factory()->create([
            'account_id' => $account1->id,
            'type' => 'credit',
            'amount' => 200000,
        ]);

        $tx2 = Transaction::factory()->create([
            'account_id' => $account2->id,
            'type' => 'credit',
            'amount' => 300000,
        ]);

        // Payment 1 to account 1
        $payment1 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account1->id,
            'income_transaction_id' => $tx1->id,
            'fee_transaction_id' => null,
            'payment_amount' => 200000,
            'amount_received' => 200000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        // Payment 2 to account 2
        $payment2 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account2->id,
            'income_transaction_id' => $tx2->id,
            'fee_transaction_id' => null,
            'payment_amount' => 300000,
            'amount_received' => 300000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Split payment void test',
        ]);

        $response->assertOk();

        // Both accounts should have reduced balances
        expect($account1->fresh()->current_balance)->toBe(300000); // $5,000 - $2,000
        expect($account2->fresh()->current_balance)->toBe(200000); // $5,000 - $3,000
    });

    test('cannot void if any account has insufficient balance', function () {
        $account1 = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 500000, // $5,000 - sufficient
        ]);

        $account2 = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // $1,000 - insufficient for $3,000 reversal
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000,
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        // Create transactions
        $tx1 = Transaction::factory()->create([
            'account_id' => $account1->id,
            'type' => 'credit',
            'amount' => 200000,
        ]);

        $tx2 = Transaction::factory()->create([
            'account_id' => $account2->id,
            'type' => 'credit',
            'amount' => 300000,
        ]);

        // Payment 1 to account 1
        $payment1 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account1->id,
            'income_transaction_id' => $tx1->id,
            'fee_transaction_id' => null,
            'payment_amount' => 200000,
            'amount_received' => 200000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        // Payment 2 to account 2 - needs $3,000 but only has $1,000
        $payment2 = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account2->id,
            'income_transaction_id' => $tx2->id,
            'fee_transaction_id' => null,
            'payment_amount' => 300000,
            'amount_received' => 300000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Should fail due to insufficient balance',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['void_reason']);

        // Verify invoice was not voided
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    });
});
