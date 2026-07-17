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
function accountApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/accounts', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/accounts')->assertUnauthorized();
    });

    it('returns 403 without accounts.read', function () {
        $this->getJson('/api/v1/accounts', accountApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists accounts in a paginated envelope', function () {
        Account::factory()->count(3)->create();

        $this->getJson('/api/v1/accounts', accountApiBearer(['accounts.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'type', 'currency_code',
                    'current_balance', 'formatted_balance',
                    'account_number', 'bank_name', 'is_active',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/accounts', function () {
    it('returns 403 without accounts.create', function () {
        $this->postJson('/api/v1/accounts', [
            'name' => 'Ops Wallet',
            'type' => 'wallet',
            'currency_code' => 'PKR',
        ], accountApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('creates an account and stores the balance in minor units', function () {
        $response = $this->postJson('/api/v1/accounts', [
            'name' => 'Ops Wallet',
            'type' => 'wallet',
            'currency_code' => 'PKR',
            'current_balance' => 1500.50, // major units on input
        ], accountApiBearer(['accounts.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'current_balance', 'formatted_balance']])
            ->assertJsonPath('data.name', 'Ops Wallet')
            ->assertJsonPath('data.current_balance', 1500.5); // major units out

        // Persisted in minor units (paisa): 1500.50 -> 150050.
        $this->assertDatabaseHas('accounts', [
            'id' => $response->json('data.id'),
            'name' => 'Ops Wallet',
            'current_balance' => 150050,
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/accounts', [
            'type' => 'not-a-type',
        ], accountApiBearer(['accounts.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type', 'currency_code']);
    });
});

describe('id-scoped account actions', function () {
    it('returns 404 for a missing account', function () {
        $this->getJson('/api/v1/accounts/999999', accountApiBearer(['accounts.read']))
            ->assertNotFound();
    });

    it('shows a single account', function () {
        $account = Account::factory()->create(['name' => 'Main Bank']);

        $this->getJson("/api/v1/accounts/{$account->id}", accountApiBearer(['accounts.read']))
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'current_balance', 'formatted_balance']])
            ->assertJsonPath('data.name', 'Main Bank');
    });

    it('updates an account', function () {
        $account = Account::factory()->create();

        $this->putJson("/api/v1/accounts/{$account->id}", [
            'name' => 'Renamed Account',
            'type' => 'bank',
            'currency_code' => 'PKR',
            'current_balance' => 2000,
        ], accountApiBearer(['accounts.update']))
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed Account');

        // 2000 major -> 200000 minor.
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Renamed Account',
            'current_balance' => 200000,
        ]);
    });

    it('deletes an account', function () {
        $account = Account::factory()->create();

        $this->deleteJson("/api/v1/accounts/{$account->id}", [], accountApiBearer(['accounts.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Account deleted successfully');

        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    });
});

describe('GET /api/v1/accounts/{id}/transactions', function () {
    it('returns 404 for a missing account', function () {
        $this->getJson('/api/v1/accounts/999999/transactions', accountApiBearer(['accounts.read']))
            ->assertNotFound();
    });

    it('lists only this account\'s transactions with account loaded so formatted_amount does not 500', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);
        $other = Account::factory()->create(['currency_code' => 'PKR']);
        Transaction::factory()->count(2)->create(['account_id' => $account->id]);
        Transaction::factory()->create(['account_id' => $other->id]);

        $this->getJson("/api/v1/accounts/{$account->id}/transactions", accountApiBearer(['accounts.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'type', 'amount', 'formatted_amount', 'account']],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.account.id', $account->id);
    });
});
