<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);

    // Invoices go through Client::factory(), which reads the world reference tables.
    seedMinimalWorld();
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token. Uniquely named to avoid Pest's
 * global-function collision with other API test files.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function invoiceApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/invoices', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/invoices')->assertUnauthorized();
    });

    it('returns 403 without invoices.read', function () {
        $this->getJson('/api/v1/invoices', invoiceApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists invoices in a paginated envelope', function () {
        Invoice::factory()->count(3)->create();

        $this->getJson('/api/v1/invoices', invoiceApiBearer(['invoices.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'invoice_number', 'status', 'total_amount', 'balance_due']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/invoices', function () {
    it('returns 403 without invoices.create', function () {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $this->postJson('/api/v1/invoices', [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'currency_code' => $client->currency->code,
            'issue_date' => '2026-01-01',
            'due_date' => '2026-01-31',
            'items' => [
                ['description' => 'Work', 'quantity' => 1, 'unit' => 'hour', 'unit_price' => 100.00],
            ],
        ], invoiceApiBearer(['invoices.read']))
            ->assertForbidden();
    });

    it('creates an invoice with line items and stores minor units', function () {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $response = $this->postJson('/api/v1/invoices', [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'currency_code' => $client->currency->code,
            'issue_date' => '2026-01-01',
            'due_date' => '2026-01-31',
            'tax_amount' => 140.00,
            'items' => [
                ['description' => 'Website Development', 'quantity' => 10, 'unit' => 'hour', 'unit_price' => 100.00],
                ['description' => 'Design Work', 'quantity' => 5, 'unit' => 'hour', 'unit_price' => 80.00],
            ],
        ], invoiceApiBearer(['invoices.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'invoice_number', 'status', 'items', 'total_amount']])
            ->assertJsonPath('data.status', 'draft');

        // ($100 * 10) + ($80 * 5) = $1,400 subtotal; +10% tax = $1,540 total, all in cents.
        $this->assertDatabaseHas('invoices', [
            'id' => $response->json('data.id'),
            'client_id' => $client->id,
            'subtotal' => 140000,
            'tax_amount' => 14000,
            'total_amount' => 154000,
            'balance_due' => 154000,
            'status' => 'draft',
        ]);
    });
});

describe('POST /api/v1/invoices/{id}/record-payment', function () {
    it('records a payment and returns an InvoicePaymentResource', function () {
        $invoice = Invoice::factory()->sent()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000, // $5,000
            'balance_due' => 500000,
        ]);

        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 0,
        ]);

        $response = $this->postJson("/api/v1/invoices/{$invoice->id}/record-payment", [
            'account_id' => $account->id,
            'payment_amount' => 5000.00,
            'amount_received' => 5000.00,
            'payment_date' => '2026-01-10',
        ], invoiceApiBearer(['invoices.update']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'account_id', 'payment_amount', 'formatted_payment_amount', 'payment_date']])
            ->assertJsonPath('data.payment_amount', fn ($amount) => (float) $amount === 5000.0);

        // Invoice is fully paid, income transaction is created in minor units.
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'amount_paid' => 500000,
            'balance_due' => 0,
        ]);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500000,
        ]);
    });
});

describe('POST /api/v1/invoices/{id}/void', function () {
    it('voids a paid invoice with sufficient account balance', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 1000000, // $10,000
        ]);

        $invoice = Invoice::factory()->create([
            'currency_code' => 'USD',
            'total_amount' => 500000,
            'amount_paid' => 500000,
            'balance_due' => 0,
            'status' => 'paid',
        ]);

        $incomeTransaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500000,
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'account_id' => $account->id,
            'income_transaction_id' => $incomeTransaction->id,
            'fee_transaction_id' => null,
            'payment_amount' => 500000,
            'amount_received' => 500000,
            'fee_amount' => 0,
            'payment_date' => now(),
        ]);

        $this->postJson("/api/v1/invoices/{$invoice->id}/void", [
            'void_reason' => 'Client requested refund',
        ], invoiceApiBearer(['invoices.update']))
            ->assertOk()
            ->assertJsonPath('data.status', 'void');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'void',
            'void_reason' => 'Client requested refund',
            'amount_paid' => 0,
            'balance_due' => 500000,
        ]);
    });
});
