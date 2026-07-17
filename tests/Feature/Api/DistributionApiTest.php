<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Distribution;
use App\Models\Role;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DistributionService;

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
function distributionApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

/**
 * Seed an active cap table summing to 100% (40/30/20/10, last as office reserve),
 * the precondition for creating/processing a distribution.
 */
function seedActiveShareholders(): void
{
    Shareholder::factory()->create(['equity_percentage' => 40, 'is_active' => true]);
    Shareholder::factory()->create(['equity_percentage' => 30, 'is_active' => true]);
    Shareholder::factory()->create(['equity_percentage' => 20, 'is_active' => true]);
    Shareholder::factory()->officeReserve()->create(['equity_percentage' => 10]);
}

describe('GET /api/v1/distributions', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/distributions')->assertUnauthorized();
    });

    it('returns 403 without distributions.read', function () {
        $this->getJson('/api/v1/distributions', distributionApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists distributions in a paginated envelope', function () {
        Distribution::factory()->count(3)->create();

        $this->getJson('/api/v1/distributions', distributionApiBearer(['distributions.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'distribution_number', 'status', 'final_net_profit']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/distributions', function () {
    it('returns 403 without distributions.create', function () {
        $this->postJson('/api/v1/distributions', [
            'manual_amount_pkr' => 5_000_000,
            'action' => 'draft',
        ], distributionApiBearer(['distributions.read']))
            ->assertForbidden();
    });

    it('creates a manual-amount draft (input in paisa, output in rupees)', function () {
        seedActiveShareholders();

        $this->postJson('/api/v1/distributions', [
            'manual_amount_pkr' => 5_000_000, // Rs 50,000 in paisa (minor units)
            'action' => 'draft',
        ], distributionApiBearer(['distributions.create']))
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            // Output money is major units: 5,000,000 paisa → 50,000 rupees.
            ->assertJsonPath('data.final_net_profit', fn ($v) => (float) $v === 50000.0)
            ->assertJsonCount(4, 'data.lines');

        $this->assertDatabaseHas('distributions', [
            'status' => 'draft',
            'adjusted_net_profit_pkr' => 5_000_000,
        ]);
    });

    it('returns 422 when shareholder equity does not total 100%', function () {
        Shareholder::factory()->create(['equity_percentage' => 60, 'is_active' => true]);

        $this->postJson('/api/v1/distributions', [
            'manual_amount_pkr' => 5_000_000,
            'action' => 'draft',
        ], distributionApiBearer(['distributions.create']))
            ->assertStatus(422);
    });
});

describe('PUT /api/v1/distributions/{id}/adjust-profit', function () {
    it('adjusts the net profit of a draft', function () {
        seedActiveShareholders();
        $distribution = Distribution::factory()->create([
            'status' => 'draft',
            'calculated_net_profit_pkr' => 10_000_000,
            'adjusted_net_profit_pkr' => null,
        ]);

        $this->putJson("/api/v1/distributions/{$distribution->id}/adjust-profit", [
            'adjusted_amount' => 6_000_000, // Rs 60,000 in paisa
            'reason' => 'Correcting an over-count',
        ], distributionApiBearer(['distributions.update']))
            ->assertOk()
            ->assertJsonPath('data.is_manually_adjusted', true)
            ->assertJsonPath('data.adjusted_net_profit_pkr', fn ($v) => (float) $v === 60000.0);

        $this->assertDatabaseHas('distributions', [
            'id' => $distribution->id,
            'adjusted_net_profit_pkr' => 6_000_000,
        ]);
    });

    // #65: adjusted_amount must reject negatives and fractional values.
    it('returns 422 for a negative adjusted amount', function () {
        $distribution = Distribution::factory()->create(['status' => 'draft']);

        $this->putJson("/api/v1/distributions/{$distribution->id}/adjust-profit", [
            'adjusted_amount' => -1,
            'reason' => 'nope',
        ], distributionApiBearer(['distributions.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['adjusted_amount']);
    });

    it('returns 422 for a fractional adjusted amount', function () {
        $distribution = Distribution::factory()->create(['status' => 'draft']);

        $this->putJson("/api/v1/distributions/{$distribution->id}/adjust-profit", [
            'adjusted_amount' => 100.5,
            'reason' => 'nope',
        ], distributionApiBearer(['distributions.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['adjusted_amount']);
    });
});

describe('POST /api/v1/distributions/{id}/process', function () {
    it('processes a draft and posts payout transactions', function () {
        seedActiveShareholders();
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 100_000_000, // Rs 1,000,000 (paisa)
        ]);

        // Arrange a draft with reconciled lines via the real service.
        $distribution = app(DistributionService::class)->createDistribution([
            'manual_amount_pkr' => 10_000_000, // Rs 100,000 (paisa)
            'action' => 'draft',
        ]);

        $this->postJson("/api/v1/distributions/{$distribution->id}/process", [
            'account_id' => $account->id,
        ], distributionApiBearer(['distributions.update']))
            ->assertOk()
            ->assertJsonPath('data.status', 'processed')
            ->assertJsonPath('data.is_processed', true);

        // Human partners (90%) receive payouts; the office reserve does not.
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id]);
    });
});

describe('id-scoped distribution reads', function () {
    it('returns 404 for a missing distribution', function () {
        $this->getJson('/api/v1/distributions/999999', distributionApiBearer(['distributions.read']))
            ->assertNotFound();
    });

    // #120: a processed distribution's lines carry real transaction_ids;
    // TransactionResource.formatted_amount reads transaction.account.currency_code.
    // The repo find() eager-loads lines.transaction.account, so the show must render
    // (>=2 lines arm the multi-row lazy-load guard) without a 500.
    it('shows a processed distribution with transaction lines without a lazy-load 500', function () {
        $account = Account::factory()->create(['currency_code' => 'PKR']);
        $distribution = Distribution::factory()->processed()->create();

        foreach (Shareholder::factory()->count(2)->create() as $shareholder) {
            $transaction = Transaction::factory()->debit()->create(['account_id' => $account->id]);
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => 1_000_000,
                'transaction_id' => $transaction->id,
            ]);
        }

        $this->getJson("/api/v1/distributions/{$distribution->id}", distributionApiBearer(['distributions.read']))
            ->assertOk()
            ->assertJsonPath('data.is_processed', true)
            ->assertJsonCount(2, 'data.lines')
            ->assertJsonStructure([
                'data' => ['lines' => [['transaction' => ['id', 'formatted_amount', 'account' => ['currency_code']]]]],
            ]);
    });
});

describe('DELETE /api/v1/distributions/{id}', function () {
    it('deletes a draft distribution', function () {
        $distribution = Distribution::factory()->create(['status' => 'draft']);

        $this->deleteJson("/api/v1/distributions/{$distribution->id}", [], distributionApiBearer(['distributions.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Distribution deleted successfully');

        $this->assertSoftDeleted('distributions', ['id' => $distribution->id]);
    });

    it('returns 422 when deleting a processed distribution', function () {
        $distribution = Distribution::factory()->processed()->create();

        $this->deleteJson("/api/v1/distributions/{$distribution->id}", [], distributionApiBearer(['distributions.delete']))
            ->assertStatus(422);
    });
});

describe('GET /api/v1/distributions/{id}/statements/{shareholderId}', function () {
    it('streams a shareholder profit statement as a PDF', function () {
        seedActiveShareholders();
        $shareholder = Shareholder::first();

        $distribution = app(DistributionService::class)->createDistribution([
            'manual_amount_pkr' => 10_000_000,
            'action' => 'draft',
        ]);

        $response = $this->get(
            "/api/v1/distributions/{$distribution->id}/statements/{$shareholder->id}",
            distributionApiBearer(['distributions.read'])
        )->assertOk();

        expect($response->headers->get('content-type'))->toContain('pdf');
    });

    it('returns 404 for a shareholder not in the distribution', function () {
        $outsider = Shareholder::factory()->inactive()->create();
        seedActiveShareholders();

        $distribution = app(DistributionService::class)->createDistribution([
            'manual_amount_pkr' => 10_000_000,
            'action' => 'draft',
        ]);

        $this->get(
            "/api/v1/distributions/{$distribution->id}/statements/{$outsider->id}",
            distributionApiBearer(['distributions.read'])
        )->assertNotFound();
    });
});
