<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;

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
function transactionsBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/transactions', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/transactions')->assertUnauthorized();
    });

    it('returns 403 without transactions.read', function () {
        $this->getJson('/api/v1/transactions', transactionsBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists transactions with account loaded so formatted_amount does not 500', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);
        Transaction::factory()->count(3)->create(['account_id' => $account->id]);

        $this->getJson('/api/v1/transactions', transactionsBearer(['transactions.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'type', 'amount', 'formatted_amount', 'account']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/transactions', function () {
    it('returns 403 without transactions.create', function () {
        $account = Account::factory()->create();

        $this->postJson('/api/v1/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 100,
            'date' => '2026-07-16',
        ], transactionsBearer(['transactions.read']))
            ->assertForbidden();
    });

    it('creates a posting, stores minor units, and updates the account balance', function () {
        $account = Account::factory()->create(['current_balance' => 0, 'currency_code' => 'PKR']);

        $response = $this->postJson('/api/v1/transactions', [
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 1500.00,
            'description' => 'API income',
            'date' => '2026-07-16',
        ], transactionsBearer(['transactions.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'type', 'amount', 'formatted_amount', 'account']])
            ->assertJsonPath('data.type', 'credit')
            ->assertJsonPath('data.amount', 1500);

        // Amount persisted in minor units (paisa): 1500.00 -> 150000.
        $this->assertDatabaseHas('transactions', [
            'id' => $response->json('data.id'),
            'account_id' => $account->id,
            'type' => 'credit',
            'amount' => 150000,
        ]);

        // Credit increases the account balance by the minor-unit amount.
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'current_balance' => 150000,
        ]);
    });
});

describe('append-only ledger', function () {
    it('exposes no update or delete route', function () {
        $user = User::factory()->superAdmin()->create();
        $headers = ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
        $transaction = Transaction::factory()->create();

        $this->putJson("/api/v1/transactions/{$transaction->id}", [], $headers)->assertNotFound();
        $this->deleteJson("/api/v1/transactions/{$transaction->id}", [], $headers)->assertNotFound();
    });
});
