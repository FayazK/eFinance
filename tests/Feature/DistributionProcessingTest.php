<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Distribution;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Services\DistributionService;

describe('Distribution Processing', function () {
    beforeEach(function () {
        $this->service = app(DistributionService::class);

        // Create shareholders totaling 100%
        $this->shareholders = [
            Shareholder::factory()->create(['equity_percentage' => 40, 'name' => 'Partner A']),
            Shareholder::factory()->create(['equity_percentage' => 30, 'name' => 'Partner B']),
            Shareholder::factory()->create(['equity_percentage' => 20, 'name' => 'Partner C']),
            Shareholder::factory()->officeReserve()->create(['equity_percentage' => 10, 'name' => 'Office Reserve']),
        ];

        // Create PKR account with sufficient balance
        $this->account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 100000000, // Rs 1,000,000
        ]);
    });

    test('processes distribution and creates transactions for human partners only', function () {
        // Create distribution with Rs 100,000 profit (10,000,000 paisa)
        $distribution = Distribution::factory()->create([
            'calculated_net_profit_pkr' => 10000000,
            'status' => 'draft',
        ]);

        // Create distribution lines
        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(10000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        // Process distribution
        $processed = $this->service->processDistribution($distribution->id, $this->account->id);

        expect($processed->status)->toBe('processed')
            ->and($processed->processed_at)->not->toBeNull();

        // Verify transactions created for human partners (40% + 30% + 20% = 90%)
        // Office (10%) should have NO transaction
        $transactionCount = Transaction::where('reference_type', Distribution::class)
            ->where('reference_id', $distribution->id)
            ->count();

        expect($transactionCount)->toBe(3); // Only 3 human partners

        // Verify Office line has no transaction
        $officeLine = $distribution->lines()
            ->whereHas('shareholder', fn ($q) => $q->where('is_office_reserve', true))
            ->first();

        expect($officeLine->transaction_id)->toBeNull();

        // Verify human partners have transactions
        $humanLines = $distribution->lines()
            ->whereHas('shareholder', fn ($q) => $q->where('is_office_reserve', false))
            ->get();

        foreach ($humanLines as $line) {
            expect($line->transaction_id)->not->toBeNull();
        }
    });

    test('account balance decreases by total human partner payouts only', function () {
        $initialBalance = $this->account->current_balance;

        $distribution = Distribution::factory()->create([
            'calculated_net_profit_pkr' => 10000000, // Rs 100,000
            'status' => 'draft',
        ]);

        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(10000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        $this->service->processDistribution($distribution->id, $this->account->id);

        // Expected decrease: 40% + 30% + 20% = 90% of 10,000,000 = 9,000,000
        // Office 10% (1,000,000) stays in company
        $this->account->refresh();
        $expectedBalance = $initialBalance - 9000000;

        expect($this->account->current_balance)->toBe($expectedBalance);
    });

    test('throws exception if insufficient balance', function () {
        // Create account with low balance
        $poorAccount = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 1000000, // Only Rs 10,000
        ]);

        $distribution = Distribution::factory()->create([
            'calculated_net_profit_pkr' => 10000000, // Rs 100,000 needed
            'status' => 'draft',
        ]);

        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(10000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        expect(fn () => $this->service->processDistribution($distribution->id, $poorAccount->id))
            ->toThrow(InvalidArgumentException::class, 'Insufficient balance');
    });

    test('cannot process already processed distribution', function () {
        $distribution = Distribution::factory()->processed()->create();

        expect(fn () => $this->service->processDistribution($distribution->id, $this->account->id))
            ->toThrow(InvalidArgumentException::class, 'already been processed');
    });

    test('must use PKR account for distribution', function () {
        $usdAccount = Account::factory()->create(['currency_code' => 'USD']);

        $distribution = Distribution::factory()->create(['status' => 'draft']);

        expect(fn () => $this->service->processDistribution($distribution->id, $usdAccount->id))
            ->toThrow(InvalidArgumentException::class, 'PKR accounts');
    });

    test('distributed amount reflects only human partner withdrawals', function () {
        $distribution = Distribution::factory()->create([
            'calculated_net_profit_pkr' => 10000000,
            'status' => 'draft',
        ]);

        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(10000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        $processed = $this->service->processDistribution($distribution->id, $this->account->id);

        // Only 90% distributed (40% + 30% + 20%), Office 10% retained
        expect($processed->distributed_amount_pkr)->toBe(9000000);
    });

    test('transaction descriptions include shareholder name and distribution number', function () {
        $distribution = Distribution::factory()->create([
            'distribution_number' => 'DIST-2026-001',
            'calculated_net_profit_pkr' => 10000000,
            'status' => 'draft',
        ]);

        foreach ($this->shareholders as $shareholder) {
            $allocatedAmount = (int) round(10000000 * ((float) $shareholder->equity_percentage / 100));
            $distribution->lines()->create([
                'shareholder_id' => $shareholder->id,
                'equity_percentage_snapshot' => $shareholder->equity_percentage,
                'allocated_amount_pkr' => $allocatedAmount,
            ]);
        }

        $this->service->processDistribution($distribution->id, $this->account->id);

        $transactions = Transaction::where('reference_type', Distribution::class)
            ->where('reference_id', $distribution->id)
            ->get();

        foreach ($transactions as $transaction) {
            expect($transaction->description)->toContain('Distribution')
                ->and($transaction->description)->toContain('DIST-2026-001')
                ->and($transaction->type)->toBe('debit');
        }
    });
});
