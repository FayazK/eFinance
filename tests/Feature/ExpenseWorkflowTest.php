<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->account = Account::factory()->create([
        'name' => 'Test Account',
        'currency_code' => 'PKR',
        'current_balance' => 100000, // 1000.00 PKR
        'is_active' => true,
    ]);

    $this->category = TransactionCategory::factory()->create([
        'name' => 'Test Expense Category',
        'type' => 'expense',
    ]);
});

describe('Expense Draft Creation', function () {
    it('creates expense as draft without creating transaction', function () {
        $expenseData = [
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 50.00,
            'currency_code' => 'PKR',
            'vendor' => 'Test Vendor',
            'description' => 'Test expense description',
            'expense_date' => now()->format('Y-m-d'),
            'is_recurring' => '0',
        ];

        $response = $this->post('/dashboard/expenses', $expenseData);

        $response->assertRedirect('/dashboard/expenses');

        $expense = Expense::latest()->first();
        expect($expense)->not->toBeNull()
            ->and($expense->status)->toBe('draft')
            ->and($expense->transaction_id)->toBeNull();

        // Account balance should not be affected
        $this->account->refresh();
        expect($this->account->current_balance)->toBe(100000);
    });

    it('stores expense amount in minor units', function () {
        $expenseData = [
            'account_id' => $this->account->id,
            'amount' => 123.45,
            'currency_code' => 'PKR',
            'expense_date' => now()->format('Y-m-d'),
            'is_recurring' => '0',
        ];

        $this->post('/dashboard/expenses', $expenseData);

        $expense = Expense::latest()->first();
        expect($expense->amount)->toBe(12345); // Minor units
    });
});

describe('Expense Draft Editing', function () {
    it('allows editing draft expenses', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 5000, // 50.00
            'currency_code' => 'PKR',
            'status' => 'draft',
            'transaction_id' => null,
        ]);

        $updateData = [
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 75.00,
            'currency_code' => 'PKR',
            'vendor' => 'Updated Vendor',
            'description' => 'Updated description',
            'expense_date' => now()->format('Y-m-d'),
        ];

        $response = $this->put("/dashboard/expenses/{$expense->id}", $updateData);

        $response->assertRedirect('/dashboard/expenses');

        $expense->refresh();
        expect($expense->amount)->toBe(7500) // 75.00 in minor units
            ->and($expense->vendor)->toBe('Updated Vendor')
            ->and($expense->description)->toBe('Updated description')
            ->and($expense->status)->toBe('draft');
    });

    it('prevents editing processed expenses', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'status' => 'processed',
        ]);

        $updateData = [
            'account_id' => $this->account->id,
            'amount' => 100.00,
            'currency_code' => 'PKR',
            'expense_date' => now()->format('Y-m-d'),
        ];

        $response = $this->put("/dashboard/expenses/{$expense->id}", $updateData);

        $response->assertForbidden();
    });

    it('shows edit page for draft expenses', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'status' => 'draft',
        ]);

        $response = $this->get("/dashboard/expenses/{$expense->id}/edit");

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/expenses/edit')
                ->has('expense')
                ->has('accounts')
                ->has('categories')
            );
    });

    it('forbids edit page for processed expenses', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'status' => 'processed',
        ]);

        $response = $this->get("/dashboard/expenses/{$expense->id}/edit");

        $response->assertForbidden();
    });
});

describe('Expense Processing', function () {
    it('processes draft expense and creates transaction', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 5000, // 50.00
            'currency_code' => 'PKR',
            'reporting_amount_pkr' => 5000,
            'status' => 'draft',
            'transaction_id' => null,
            'expense_date' => now(),
        ]);

        $initialBalance = $this->account->current_balance;

        $response = $this->post("/dashboard/expenses/{$expense->id}/process");

        $response->assertRedirect('/dashboard/expenses');

        $expense->refresh();
        expect($expense->status)->toBe('processed')
            ->and($expense->transaction_id)->not->toBeNull();

        // Transaction should exist
        $transaction = Transaction::find($expense->transaction_id);
        expect($transaction)->not->toBeNull()
            ->and($transaction->type)->toBe('debit')
            ->and($transaction->amount)->toBe(5000);

        // Account balance should be reduced
        $this->account->refresh();
        expect($this->account->current_balance)->toBe($initialBalance - 5000);
    });

    it('prevents processing already processed expense', function () {
        $expense = Expense::factory()->create([
            'account_id' => $this->account->id,
            'status' => 'processed',
        ]);

        $response = $this->post("/dashboard/expenses/{$expense->id}/process");

        $response->assertRedirect()
            ->assertSessionHasErrors();
    });
});

describe('Expense Foreign Currency', function () {
    it('creates draft expense with exchange rate for foreign currency', function () {
        $usdAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // 1000.00 USD
            'is_active' => true,
        ]);

        $expenseData = [
            'account_id' => $usdAccount->id,
            'amount' => 100.00,
            'currency_code' => 'USD',
            'exchange_rate' => 280.50,
            'expense_date' => now()->format('Y-m-d'),
            'is_recurring' => '0',
        ];

        $this->post('/dashboard/expenses', $expenseData);

        $expense = Expense::latest()->first();
        expect($expense)->not->toBeNull()
            ->and($expense->status)->toBe('draft')
            ->and($expense->exchange_rate)->toBe('280.5000')
            ->and($expense->reporting_amount_pkr)->toBe(2805000); // 100 * 280.50 * 100
    });

    it('updates reporting amount when editing exchange rate', function () {
        $usdAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000,
            'is_active' => true,
        ]);

        $expense = Expense::factory()->create([
            'account_id' => $usdAccount->id,
            'amount' => 10000, // 100.00 USD
            'currency_code' => 'USD',
            'exchange_rate' => 280.00,
            'reporting_amount_pkr' => 2800000,
            'status' => 'draft',
        ]);

        $updateData = [
            'account_id' => $usdAccount->id,
            'amount' => 100.00,
            'currency_code' => 'USD',
            'exchange_rate' => 285.00,
            'expense_date' => now()->format('Y-m-d'),
        ];

        $this->put("/dashboard/expenses/{$expense->id}", $updateData);

        $expense->refresh();
        expect($expense->reporting_amount_pkr)->toBe(2850000); // 100 * 285 * 100
    });
});
