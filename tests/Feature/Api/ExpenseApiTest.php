<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Expense;
use App\Models\Role;
use App\Models\User;
use App\Services\ExpenseService;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function expenseApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

/**
 * Arrange a processed expense (draft → process) against a funded PKR account,
 * using the real service so the ledger transaction actually exists.
 */
function seedProcessedExpense(): Expense
{
    $account = Account::factory()->create([
        'currency_code' => 'PKR',
        'current_balance' => 100_000_000, // Rs 1,000,000 in paisa
        'is_active' => true,
    ]);

    $service = app(ExpenseService::class);
    $expense = $service->createDraftExpense([
        'account_id' => $account->id,
        'amount' => 500, // Rs 500 (major units)
        'currency_code' => 'PKR',
        'expense_date' => now()->format('Y-m-d'),
    ]);

    return $service->processExpense($expense->id);
}

describe('GET /api/v1/expenses', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/expenses')->assertUnauthorized();
    });

    it('returns 403 without expenses.read', function () {
        $this->getJson('/api/v1/expenses', expenseApiBearer(['distributions.read']))
            ->assertForbidden();
    });

    it('lists expenses in a paginated envelope', function () {
        Expense::factory()->count(3)->create();

        $this->getJson('/api/v1/expenses', expenseApiBearer(['expenses.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'amount', 'currency_code', 'formatted_amount', 'status']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/expenses', function () {
    it('returns 403 without expenses.create', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);

        $this->postJson('/api/v1/expenses', [
            'account_id' => $account->id,
            'amount' => 100,
            'currency_code' => 'PKR',
            'expense_date' => now()->format('Y-m-d'),
        ], expenseApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('records a draft expense, storing the amount in minor units (paisa)', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);

        $response = $this->postJson('/api/v1/expenses', [
            'account_id' => $account->id,
            'amount' => 1500.50, // major units (rupees)
            'currency_code' => 'PKR',
            'vendor' => 'Office Supplies Co',
            'expense_date' => now()->format('Y-m-d'),
        ], expenseApiBearer(['expenses.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'amount', 'status', 'formatted_amount', 'account']])
            ->assertJsonPath('data.status', 'draft')
            // Response amount is raw paisa: 1500.50 rupees → 150050 paisa.
            ->assertJsonPath('data.amount', 150050);

        $this->assertDatabaseHas('expenses', [
            'id' => $response->json('data.id'),
            'amount' => 150050,
            'status' => 'draft',
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/expenses', [
            'vendor' => 'Nobody',
        ], expenseApiBearer(['expenses.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['account_id', 'amount', 'currency_code', 'expense_date']);
    });

    it('returns 422 when the expense currency does not match the account currency', function () {
        $account = Account::factory()->create(['currency_code' => 'USD']);

        $this->postJson('/api/v1/expenses', [
            'account_id' => $account->id,
            'amount' => 100,
            'currency_code' => 'PKR', // mismatch: account is USD
            'expense_date' => now()->format('Y-m-d'),
        ], expenseApiBearer(['expenses.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['account_id']);
    });
});

describe('id-scoped expense reads', function () {
    it('returns 404 for a missing expense', function () {
        $this->getJson('/api/v1/expenses/999999', expenseApiBearer(['expenses.read']))
            ->assertNotFound();
    });

    it('shows a single expense with its account relation loaded', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);
        $expense = Expense::factory()->create(['account_id' => $account->id]);

        $this->getJson("/api/v1/expenses/{$expense->id}", expenseApiBearer(['expenses.read']))
            ->assertOk()
            ->assertJsonPath('data.id', $expense->id)
            ->assertJsonPath('data.account.id', $account->id);
    });
});

describe('POST /api/v1/expenses/{id}/process', function () {
    it('processes a draft expense and posts a ledger transaction', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 100_000_000,
            'is_active' => true,
        ]);
        $expense = app(ExpenseService::class)->createDraftExpense([
            'account_id' => $account->id,
            'amount' => 500,
            'currency_code' => 'PKR',
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $this->postJson("/api/v1/expenses/{$expense->id}/process", [], expenseApiBearer(['expenses.update']))
            ->assertOk()
            ->assertJsonPath('data.status', 'processed');

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);
    });

    it('returns 404 for a missing expense', function () {
        $this->postJson('/api/v1/expenses/999999/process', [], expenseApiBearer(['expenses.update']))
            ->assertNotFound();
    });
});

describe('POST /api/v1/expenses/{id}/void', function () {
    it('voids a processed expense and returns the updated resource', function () {
        $expense = seedProcessedExpense();

        $this->postJson("/api/v1/expenses/{$expense->id}/void", [
            'void_reason' => 'Recorded in error',
        ], expenseApiBearer(['expenses.update']))
            ->assertOk()
            ->assertJsonPath('data.status', 'voided')
            ->assertJsonPath('data.is_voided', true);
    });

    it('returns 422 when voiding a non-processed (draft) expense', function () {
        $expense = Expense::factory()->create(['status' => 'draft']);

        $this->postJson("/api/v1/expenses/{$expense->id}/void", [
            'void_reason' => 'Too soon',
        ], expenseApiBearer(['expenses.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['void_reason']);
    });
});

describe('DELETE /api/v1/expenses/{id}', function () {
    it('discards a draft expense (soft delete)', function () {
        $expense = Expense::factory()->create(['status' => 'draft']);

        $this->deleteJson("/api/v1/expenses/{$expense->id}", [], expenseApiBearer(['expenses.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Expense deleted successfully');

        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    });

    it('returns 422 when deleting a non-draft (processed) expense', function () {
        $expense = Expense::factory()->processed()->create();

        $this->deleteJson("/api/v1/expenses/{$expense->id}", [], expenseApiBearer(['expenses.delete']))
            ->assertStatus(422);
    });
});

describe('GET /api/v1/expenses/last-exchange-rate/{currency}', function () {
    it('returns the last exchange rate for a currency', function () {
        $this->getJson('/api/v1/expenses/last-exchange-rate/USD', expenseApiBearer(['expenses.read']))
            ->assertOk()
            ->assertJsonStructure(['rate']);
    });
});
