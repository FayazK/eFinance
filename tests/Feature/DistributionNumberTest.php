<?php

declare(strict_types=1);

use App\Models\Distribution;
use App\Models\Shareholder;
use App\Services\DistributionService;

describe('Distribution Number Generation', function () {
    beforeEach(function () {
        $this->service = app(DistributionService::class);

        // Shareholders must total 100% or createDistribution() throws.
        Shareholder::factory()->create(['equity_percentage' => 40]);
        Shareholder::factory()->create(['equity_percentage' => 30]);
        Shareholder::factory()->create(['equity_percentage' => 20]);
        Shareholder::factory()->officeReserve()->create(['equity_percentage' => 10]);
    });

    test('continues the sequence past 999 instead of resetting to 001', function () {
        // The factory caps its sequence at 999, so seed the trigger row explicitly.
        Distribution::factory()->create([
            'distribution_number' => 'DIST-'.now()->year.'-1000',
        ]);

        $distribution = $this->service->createDistribution([
            'manual_amount_pkr' => 100000,
        ]);

        expect($distribution->distribution_number)->toBe('DIST-'.now()->year.'-1001');
    });
});
