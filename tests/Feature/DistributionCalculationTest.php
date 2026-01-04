<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Distribution;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Services\DistributionService;

describe('Distribution Calculation', function () {
    beforeEach(function () {
        $this->service = app(DistributionService::class);

        // Create shareholders totaling 100%
        $this->shareholders = [
            Shareholder::factory()->create(['equity_percentage' => 40, 'name' => 'Partner A']),
            Shareholder::factory()->create(['equity_percentage' => 30, 'name' => 'Partner B']),
            Shareholder::factory()->create(['equity_percentage' => 20, 'name' => 'Partner C']),
            Shareholder::factory()->officeReserve()->create(['equity_percentage' => 10, 'name' => 'Office Reserve']),
        ];

        // Create PKR account for transactions
        $this->account = Account::factory()->create(['currency_code' => 'PKR']);
    });

    test('calculates profit correctly from transactions', function () {
        // Create income transactions (credits) totaling Rs 500,000 (in minor units: 50,000,000)
        Transaction::factory()->count(3)->create([
            'account_id' => $this->account->id,
            'type' => 'credit',
            'reporting_amount_pkr' => 15000000, // Rs 150,000
            'date' => now()->subDays(15),
        ]);
        Transaction::factory()->create([
            'account_id' => $this->account->id,
            'type' => 'credit',
            'reporting_amount_pkr' => 5000000, // Rs 50,000
            'date' => now()->subDays(10),
        ]);

        // Create expense transactions (debits) totaling Rs 200,000 (in minor units: 20,000,000)
        Transaction::factory()->count(2)->create([
            'account_id' => $this->account->id,
            'type' => 'debit',
            'reporting_amount_pkr' => 10000000, // Rs 100,000 each
            'date' => now()->subDays(12),
        ]);

        // Expected: Revenue = 50,000,000, Expenses = 20,000,000, Profit = 30,000,000
        $distribution = $this->service->createDistribution([
            'period_start' => now()->subMonth()->format('Y-m-d'),
            'period_end' => now()->format('Y-m-d'),
        ]);

        expect($distribution->total_revenue_pkr)->toBe(50000000)
            ->and($distribution->total_expenses_pkr)->toBe(20000000)
            ->and($distribution->calculated_net_profit_pkr)->toBe(30000000);
    });

    test('creates distribution lines for all active shareholders', function () {
        $distribution = Distribution::factory()->create([
            'calculated_net_profit_pkr' => 1000000, // Rs 10,000
        ]);

        // Manually create lines using service's private method logic
        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(1000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        $distribution->load('lines');

        expect($distribution->lines)->toHaveCount(4);

        // Verify allocations: 40% = 400,000, 30% = 300,000, 20% = 200,000, 10% = 100,000
        expect($distribution->lines[0]->allocated_amount_pkr)->toBe(400000)
            ->and($distribution->lines[1]->allocated_amount_pkr)->toBe(300000)
            ->and($distribution->lines[2]->allocated_amount_pkr)->toBe(200000)
            ->and($distribution->lines[3]->allocated_amount_pkr)->toBe(100000);
    });

    test('manual adjustment recalculates distribution lines', function () {
        // Create distribution with Rs 100,000 profit
        Transaction::factory()->create([
            'type' => 'credit',
            'reporting_amount_pkr' => 10000000, // Rs 100,000
            'date' => now()->subDays(5),
        ]);

        $distribution = $this->service->createDistribution([
            'period_start' => now()->subMonth()->format('Y-m-d'),
            'period_end' => now()->format('Y-m-d'),
        ]);

        // Adjust profit to Rs 80,000 (in minor units: 8,000,000)
        $adjusted = $this->service->adjustNetProfit(
            $distribution->id,
            8000000,
            'Holding back extra reserves'
        );

        expect($adjusted->adjusted_net_profit_pkr)->toBe(8000000)
            ->and($adjusted->adjustment_reason)->toBe('Holding back extra reserves')
            ->and($adjusted->is_manually_adjusted)->toBeTrue();

        // Verify lines recalculated: Partner A (40%) should get 3,200,000
        $partnerALine = $adjusted->lines()->whereHas('shareholder', fn ($q) => $q->where('name', 'Partner A'))->first();
        expect($partnerALine->allocated_amount_pkr)->toBe(3200000);
    });

    test('cannot create distribution if equity does not total 100%', function () {
        // Delete all shareholders and create invalid total
        Shareholder::query()->delete();
        Shareholder::factory()->create(['equity_percentage' => 60]);

        expect(fn () => $this->service->createDistribution([
            'period_start' => now()->subMonth()->format('Y-m-d'),
            'period_end' => now()->format('Y-m-d'),
        ]))->toThrow(InvalidArgumentException::class, 'must be 100%');
    });

    test('validates period dates', function () {
        expect(fn () => $this->service->createDistribution([
            'period_start' => now()->format('Y-m-d'),
            'period_end' => now()->subMonth()->format('Y-m-d'), // End before start
        ]))->toThrow(InvalidArgumentException::class, 'before end date');
    });

    test('only draft distributions can be edited', function () {
        $distribution = Distribution::factory()->processed()->create();

        expect(fn () => $this->service->updateDistribution($distribution->id, [
            'notes' => 'Trying to edit',
        ]))->toThrow(InvalidArgumentException::class, 'Only draft');
    });

    test('only draft distributions can be adjusted', function () {
        $distribution = Distribution::factory()->processed()->create();

        expect(fn () => $this->service->adjustNetProfit($distribution->id, 1000000, 'reason'))
            ->toThrow(InvalidArgumentException::class, 'Cannot adjust processed');
    });

    test('only draft distributions can be deleted', function () {
        $distribution = Distribution::factory()->processed()->create();

        expect(fn () => $this->service->deleteDistribution($distribution->id))
            ->toThrow(InvalidArgumentException::class, 'Cannot delete processed');
    });

    test('can delete draft distributions', function () {
        $distribution = Distribution::factory()->create(['status' => 'draft']);

        $result = $this->service->deleteDistribution($distribution->id);

        expect($result)->toBeTrue()
            ->and(Distribution::find($distribution->id))->toBeNull();
    });
});
