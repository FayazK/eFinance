<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Transaction Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/transactions')->assertRedirect('/login');
    });

    test('authenticated users can visit the transactions index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/transactions')->assertOk();
    });
});

describe('Transaction Create', function () {
    test('authenticated users can visit the create transaction page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/transactions/create')->assertOk();
    });

    test('credit transaction increases account balance', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create([
            'current_balance' => 100000, // $1000.00
        ]);

        $response = $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500.50, // Major units
            'description' => 'Client payment received',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Transaction recorded successfully');

        // Verify account balance was updated
        $account->refresh();
        expect($account->current_balance)->toBe(150050); // 100000 + 50050 = 150050 cents

        // Verify transaction was created with correct amount
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 50050, // 500.50 * 100
            'description' => 'Client payment received',
        ]);
    });

    test('debit transaction decreases account balance', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create([
            'current_balance' => 100000, // $1000.00
        ]);

        $response = $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 250.75, // Major units
            'description' => 'Office rent payment',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        // Verify account balance was updated
        $account->refresh();
        expect($account->current_balance)->toBe(74925); // 100000 - 25075 = 74925 cents

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 25075, // 250.75 * 100
        ]);
    });

    test('transaction can be created with category', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();
        $category = TransactionCategory::factory()->income()->create();

        $response = $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'credit',
            'amount' => 100.00,
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);
    });

    test('transaction creation requires account, type, amount, and date', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/transactions', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['account_id', 'type', 'amount', 'date']);
    });

    test('transaction amount must be positive', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        $response = $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => -50.00,
            'date' => '2025-12-27',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount']);
    });

    test('transaction type must be credit or debit', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        $response = $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'invalid_type',
            'amount' => 100.00,
            'date' => '2025-12-27',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['type']);
    });
});

describe('Transaction Balance Updates', function () {
    test('multiple sequential transactions update balance correctly', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create([
            'current_balance' => 0,
        ]);

        // Credit $1000
        $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 1000.00,
            'date' => '2025-12-27',
        ]);

        $account->refresh();
        expect($account->current_balance)->toBe(100000); // $1000

        // Debit $250
        $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 250.00,
            'date' => '2025-12-27',
        ]);

        $account->refresh();
        expect($account->current_balance)->toBe(75000); // $750

        // Credit $500
        $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 500.00,
            'date' => '2025-12-27',
        ]);

        $account->refresh();
        expect($account->current_balance)->toBe(125000); // $1250
    });

    test('account can have negative balance after debit', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create([
            'current_balance' => 10000, // $100.00
        ]);

        $this->postJson('/dashboard/transactions', [
            'account_id' => $account->id,
            'type' => 'debit',
            'amount' => 200.00,
            'date' => '2025-12-27',
        ]);

        $account->refresh();
        expect($account->current_balance)->toBe(-10000); // -$100.00
    });
});

describe('Transaction Data Fetching', function () {
    test('transactions are returned with pagination', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();
        Transaction::factory()->count(20)->create(['account_id' => $account->id]);

        $response = $this->getJson('/dashboard/transactions/data?per_page=10');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page'],
            'links',
        ]);
        $response->assertJsonCount(10, 'data');
    });

    test('account transactions endpoint filters by account', function () {
        $this->actingAs($this->user);

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        Transaction::factory()->count(5)->create(['account_id' => $account1->id]);
        Transaction::factory()->count(3)->create(['account_id' => $account2->id]);

        $response = $this->getJson("/dashboard/accounts/{$account1->id}/transactions?per_page=10");

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    });
});

describe('Transaction Accessors', function () {
    test('formatted_amount accessor includes currency symbol', function () {
        $account = Account::factory()->create(['currency_code' => 'USD']);
        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 125050, // $1250.50
        ]);

        $transaction->load('account');
        expect($transaction->formatted_amount)->toContain('USD');
        expect($transaction->formatted_amount)->toContain('1,250.50');
    });
});
