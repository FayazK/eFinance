<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Role;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;

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
function transferApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

/**
 * Arrange a real same-currency transfer with an implicit fee (send $1000, receive $900)
 * through the actual service, so the withdrawal, deposit, and fee ledger legs all exist.
 */
function seedFeeTransfer(): Transfer
{
    $source = Account::factory()->create([
        'currency_code' => 'USD',
        'current_balance' => 500_000, // $5,000.00 in cents
        'is_active' => true,
    ]);
    $destination = Account::factory()->create([
        'currency_code' => 'USD',
        'current_balance' => 0,
        'is_active' => true,
    ]);

    return app(TransferService::class)->createTransfer([
        'source_account_id' => $source->id,
        'destination_account_id' => $destination->id,
        'source_amount' => 1000.00,
        'destination_amount' => 900.00,
        'description' => 'Fee transfer',
        'date' => now()->format('Y-m-d'),
    ]);
}

describe('GET /api/v1/transfers', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/transfers')->assertUnauthorized();
    });

    it('returns 403 without transfers.read', function () {
        $this->getJson('/api/v1/transfers', transferApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists transfers in a paginated envelope', function () {
        Transfer::factory()->count(3)->create();

        $this->getJson('/api/v1/transfers', transferApiBearer(['transfers.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'source_amount', 'destination_amount', 'exchange_rate', 'has_fee']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('lists same-currency fee transfers without a lazy-load error', function () {
        // Two fee transfers arm the multi-row lazy-load guard; the list must eager-load
        // feeTransaction.account (formatted_fee reads the fee transaction's account currency).
        seedFeeTransfer();
        seedFeeTransfer();

        $this->getJson('/api/v1/transfers', transferApiBearer(['transfers.read']))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.has_fee', true);
    });
});

describe('POST /api/v1/transfers', function () {
    it('returns 403 without transfers.create', function () {
        $source = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 500_000]);
        $destination = Account::factory()->create(['currency_code' => 'USD']);

        $this->postJson('/api/v1/transfers', [
            'source_account_id' => $source->id,
            'destination_account_id' => $destination->id,
            'source_amount' => 100,
            'destination_amount' => 100,
            'date' => now()->format('Y-m-d'),
        ], transferApiBearer(['transfers.read']))
            ->assertForbidden();
    });

    it('creates a same-currency transfer with no fee', function () {
        $source = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 100_000]);
        $destination = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 50_000]);

        $response = $this->postJson('/api/v1/transfers', [
            'source_account_id' => $source->id,
            'destination_account_id' => $destination->id,
            'source_amount' => 500.00,
            'destination_amount' => 500.00,
            'description' => 'Rent',
            'date' => now()->format('Y-m-d'),
        ], transferApiBearer(['transfers.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'source_amount', 'destination_amount', 'exchange_rate', 'has_fee']])
            ->assertJsonPath('data.source_amount', 500)
            ->assertJsonPath('data.destination_amount', 500)
            ->assertJsonPath('data.exchange_rate', 1)
            ->assertJsonPath('data.has_fee', false);

        $this->assertDatabaseHas('transfers', [
            'id' => $response->json('data.id'),
            'fee_transaction_id' => null,
        ]);
    });

    it('reports the true source amount and rate for a same-currency fee transfer (#47)', function () {
        $source = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 200_000]);
        $destination = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 0]);

        // Send $1000, receive $900 → implicit fee of $100, same-currency rate of 1.0 (not 0.9).
        $this->postJson('/api/v1/transfers', [
            'source_account_id' => $source->id,
            'destination_account_id' => $destination->id,
            'source_amount' => 1000.00,
            'destination_amount' => 900.00,
            'date' => now()->format('Y-m-d'),
        ], transferApiBearer(['transfers.create']))
            ->assertCreated()
            ->assertJsonPath('data.source_amount', 1000)
            ->assertJsonPath('data.destination_amount', 900)
            ->assertJsonPath('data.fee_amount', 100)
            ->assertJsonPath('data.exchange_rate', 1)
            ->assertJsonPath('data.has_fee', true);

        // Ledger: source out $1000 total ($900 transfer + $100 fee).
        $source->refresh();
        expect($source->current_balance)->toBe(100_000);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/transfers', [], transferApiBearer(['transfers.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'source_account_id', 'destination_account_id', 'source_amount', 'destination_amount', 'date',
            ]);
    });

    it('returns 422 when the source account has insufficient balance', function () {
        $source = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 5_000]); // $50
        $destination = Account::factory()->create(['currency_code' => 'USD']);

        $this->postJson('/api/v1/transfers', [
            'source_account_id' => $source->id,
            'destination_account_id' => $destination->id,
            'source_amount' => 1000.00, // more than the $50 available
            'destination_amount' => 1000.00,
            'date' => now()->format('Y-m-d'),
        ], transferApiBearer(['transfers.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['source_amount']);
    });

    it('returns 422 when source and destination are the same account', function () {
        $account = Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 100_000]);

        $this->postJson('/api/v1/transfers', [
            'source_account_id' => $account->id,
            'destination_account_id' => $account->id,
            'source_amount' => 100.00,
            'destination_amount' => 100.00,
            'date' => now()->format('Y-m-d'),
        ], transferApiBearer(['transfers.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['source_account_id']);
    });
});

describe('id-scoped transfer reads', function () {
    it('returns 404 for a missing transfer', function () {
        $this->getJson('/api/v1/transfers/999999', transferApiBearer(['transfers.read']))
            ->assertNotFound();
    });

    it('shows a single transfer with its source and destination accounts', function () {
        $transfer = Transfer::factory()->create();

        $this->getJson("/api/v1/transfers/{$transfer->id}", transferApiBearer(['transfers.read']))
            ->assertOk()
            ->assertJsonPath('data.id', $transfer->id)
            ->assertJsonPath('data.source_account.currency_code', 'USD')
            ->assertJsonPath('data.destination_account.currency_code', 'PKR');
    });

    it('shows a same-currency fee transfer without a lazy-load error', function () {
        $transfer = seedFeeTransfer();

        $this->getJson("/api/v1/transfers/{$transfer->id}", transferApiBearer(['transfers.read']))
            ->assertOk()
            ->assertJsonPath('data.has_fee', true)
            ->assertJsonPath('data.fee_amount', 100)
            ->assertJsonPath('data.source_amount', 1000);
    });
});
