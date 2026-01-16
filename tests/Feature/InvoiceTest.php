<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\Currency;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed world package data
    $this->artisan('db:seed', ['--class' => 'WorldSeeder']);
});

describe('Invoice Creation', function () {
    test('user can create invoice with line items', function () {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $response = $this->postJson('/dashboard/invoices', [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'currency_code' => $client->currency->code,
            'issue_date' => '2025-01-01',
            'due_date' => '2025-01-31',
            'tax_amount' => 140.00, // 10% of $1,400
            'items' => [
                [
                    'description' => 'Website Development',
                    'quantity' => 10,
                    'unit' => 'hour',
                    'unit_price' => 100.00,
                ],
                [
                    'description' => 'Design Work',
                    'quantity' => 5,
                    'unit' => 'hour',
                    'unit_price' => 80.00,
                ],
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('invoices', [
            'client_id' => $client->id,
            'subtotal' => 140000, // ($100 * 10) + ($80 * 5) = $1,400 in cents
            'tax_amount' => 14000, // 10% of $1,400 = $140 in cents
            'total_amount' => 154000, // $1,540 in cents
            'balance_due' => 154000,
            'status' => 'draft',
        ]);

        $invoice = Invoice::first();
        expect($invoice->items)->toHaveCount(2);
        expect($invoice->invoice_number)->toStartWith('INV-' . now()->format('Y') . '-');
    });

    test('invoice requires at least one line item', function () {
        $client = Client::factory()->create();

        $response = $this->postJson('/dashboard/invoices', [
            'client_id' => $client->id,
            'currency_code' => $client->currency->code,
            'issue_date' => '2025-01-01',
            'due_date' => '2025-01-31',
            'items' => [],
        ]);

        $response->assertUnprocessable();
    });

    test('invoice inherits client currency', function () {
        $pkrCurrency = Currency::create([
            'id' => 2,
            'name' => 'Pakistani rupee',
            'code' => 'PKR',
            'symbol' => 'Rs',
            'symbol_native' => 'Rs',
            'country_id' => 1,
            'precision' => 2,
        ]);

        $company = Company::factory()->create();
        $client = Client::factory()->create(['currency_id' => $pkrCurrency->id]);

        $response = $this->postJson('/dashboard/invoices', [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'currency_code' => 'PKR',
            'issue_date' => '2025-01-01',
            'due_date' => '2025-01-31',
            'items' => [
                [
                    'description' => 'Service',
                    'quantity' => 1,
                    'unit' => 'unit',
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('invoices', [
            'client_id' => $client->id,
            'currency_code' => 'PKR',
        ]);
    });
});

describe('Invoice Update', function () {
    test('user can update draft invoice', function () {
        $invoice = Invoice::factory()->create(['status' => 'draft']);

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}", [
            'client_id' => $invoice->client_id,
            'currency_code' => $invoice->currency_code,
            'issue_date' => $invoice->issue_date->format('Y-m-d'),
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'notes' => 'Updated notes',
            'items' => [
                [
                    'description' => 'Updated Service',
                    'quantity' => 2,
                    'unit' => 'unit',
                    'unit_price' => 150.00,
                ],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'notes' => 'Updated notes',
        ]);
    });

    test('user cannot update sent invoice', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->putJson("/dashboard/invoices/{$invoice->id}", [
            'client_id' => $invoice->client_id,
            'issue_date' => $invoice->issue_date->format('Y-m-d'),
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'items' => [],
        ]);

        $response->assertForbidden();
    });
});

describe('Invoice Deletion', function () {
    test('user can delete draft invoice', function () {
        $invoice = Invoice::factory()->create(['status' => 'draft']);

        $response = $this->deleteJson("/dashboard/invoices/{$invoice->id}");

        $response->assertOk();
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    });

    test('user cannot delete sent invoice', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->deleteJson("/dashboard/invoices/{$invoice->id}");

        $response->assertForbidden();
    });
});

describe('Payment Recording', function () {
    test('recording full payment creates income transaction', function () {
        $invoice = Invoice::factory()->sent()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5000
            'balance_due' => 500000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 5000.00,
            'amount_received' => 5000.00,
            'payment_date' => '2025-01-10',
        ]);

        $response->assertCreated();

        // Verify invoice is marked as paid
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'amount_paid' => 500000,
            'balance_due' => 0,
        ]);

        // Verify income transaction created
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500000,
        ]);

        // Verify account balance updated
        expect($account->fresh()->current_balance)->toBe(500000);
    });

    test('recording payment with fee creates two transactions', function () {
        $invoice = Invoice::factory()->sent()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5000
            'balance_due' => 500000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 5000.00,
            'amount_received' => 4750.00, // $250 fee
            'payment_date' => '2025-01-10',
        ]);

        $response->assertCreated();

        // Verify TWO transactions created
        $this->assertDatabaseHas('transactions', [
            'type' => 'credit',
            'amount' => 500000, // Full $5000 revenue
        ]);

        $bankChargesCategory = TransactionCategory::where('name', 'Bank Charges & Fees')->first();

        $this->assertDatabaseHas('transactions', [
            'type' => 'debit',
            'amount' => 25000, // $250 fee
            'category_id' => $bankChargesCategory->id,
        ]);

        // Verify account balance is net amount
        expect($account->fresh()->current_balance)->toBe(475000); // $4750
    });

    test('recording partial payment keeps invoice open', function () {
        $invoice = Invoice::factory()->sent()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5000
            'balance_due' => 500000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 3000.00, // Partial payment
            'amount_received' => 3000.00,
            'payment_date' => '2025-01-10',
        ]);

        $response->assertCreated();

        // Verify invoice status is partial
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partial',
            'amount_paid' => 300000,
            'balance_due' => 200000, // $2000 remaining
        ]);
    });

    test('payment must match invoice currency', function () {
        $invoice = Invoice::factory()->sent()->create([
            'currency_code' => 'USD',
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'PKR', // Different currency
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 5000.00,
            'amount_received' => 5000.00,
            'payment_date' => '2025-01-10',
        ]);

        $response->assertUnprocessable();
    });

    test('recording full payment on draft invoice marks it as paid', function () {
        $invoice = Invoice::factory()->create([
            'status' => 'draft',
            'currency_code' => 'USD',
            'total_amount' => 100000, // $1000
            'balance_due' => 100000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 1000.00,
            'amount_received' => 1000.00,
            'payment_date' => '2025-01-10',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'amount_paid' => 100000,
            'balance_due' => 0,
        ]);

        expect($account->fresh()->current_balance)->toBe(100000);
    });

    test('recording partial payment on draft invoice marks it as partial', function () {
        $invoice = Invoice::factory()->create([
            'status' => 'draft',
            'currency_code' => 'USD',
            'total_amount' => 100000, // $1000
            'balance_due' => 100000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 500.00, // Partial payment
            'amount_received' => 500.00,
            'payment_date' => '2025-01-10',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partial',
            'amount_paid' => 50000,
            'balance_due' => 50000,
        ]);
    });
});

describe('Status Transitions', function () {
    test('invoice status can change from draft to sent', function () {
        $invoice = Invoice::factory()->create(['status' => 'draft']);

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/change-status", [
            'status' => 'sent',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
        ]);
    });

    test('paid invoice cannot change status', function () {
        $invoice = Invoice::factory()->paid()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/change-status", [
            'status' => 'draft',
        ]);

        $response->assertUnprocessable();
    });
});

describe('Void Invoice', function () {
    test('user can void unpaid invoice', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void");

        $response->assertOk();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'void',
        ]);
        expect($invoice->fresh()->voided_at)->not->toBeNull();
    });

    test('cannot void paid invoice', function () {
        $invoice = Invoice::factory()->paid()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void");

        $response->assertForbidden();
    });
});

describe('Overdue Detection', function () {
    test('command marks overdue invoices', function () {
        // Create sent invoice with past due date
        $overdueInvoice = Invoice::factory()->create([
            'status' => 'sent',
            'issue_date' => now()->subDays(45),
            'due_date' => now()->subDays(15),
            'balance_due' => 100000,
        ]);

        // Create invoice not yet due
        $currentInvoice = Invoice::factory()->create([
            'status' => 'sent',
            'due_date' => now()->addDays(15),
            'balance_due' => 100000,
        ]);

        $this->artisan('invoices:mark-overdue')
            ->assertSuccessful();

        expect($overdueInvoice->fresh()->status)->toBe('overdue');
        expect($currentInvoice->fresh()->status)->toBe('sent');
    });

    test('paid invoices are not marked overdue', function () {
        $paidInvoice = Invoice::factory()->paid()->create([
            'due_date' => now()->subDays(30),
        ]);

        $this->artisan('invoices:mark-overdue')
            ->assertSuccessful();

        expect($paidInvoice->fresh()->status)->toBe('paid');
    });
});

describe('PDF Generation', function () {
    test('user can generate invoice PDF', function () {
        $invoice = Invoice::factory()->sent()->create();
        InvoiceItem::factory()->count(3)->create(['invoice_id' => $invoice->id]);

        $response = $this->get("/dashboard/invoices/{$invoice->id}/pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    });
});

describe('Invoice Relationships', function () {
    test('invoice belongs to client', function () {
        $invoice = Invoice::factory()->create();

        expect($invoice->client)->toBeInstanceOf(Client::class);
    });

    test('invoice can belong to project', function () {
        $project = Project::factory()->create();
        $invoice = Invoice::factory()->create(['project_id' => $project->id]);

        expect($invoice->project)->toBeInstanceOf(Project::class);
    });

    test('invoice has many items', function () {
        $invoice = Invoice::factory()->create();
        InvoiceItem::factory()->count(5)->create(['invoice_id' => $invoice->id]);

        expect($invoice->items)->toHaveCount(5);
    });
});
