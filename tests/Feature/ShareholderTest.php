<?php

declare(strict_types=1);

use App\Models\DistributionLine;
use App\Models\Shareholder;
use App\Services\ShareholderService;

describe('Shareholder Management', function () {
    beforeEach(function () {
        $this->service = app(ShareholderService::class);
    });

    test('can create shareholder with valid equity percentage', function () {
        $shareholder = $this->service->createShareholder([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'equity_percentage' => 25.50,
            'is_office_reserve' => false,
            'is_active' => true,
        ]);

        expect($shareholder)->toBeInstanceOf(Shareholder::class)
            ->and($shareholder->name)->toBe('John Doe')
            ->and($shareholder->equity_percentage)->toBe('25.50')
            ->and($shareholder->is_active)->toBeTrue();
    });

    test('equity validation rejects total exceeding 100%', function () {
        // Create shareholders totaling 90%
        Shareholder::factory()->create(['equity_percentage' => 50, 'is_active' => true]);
        Shareholder::factory()->create(['equity_percentage' => 40, 'is_active' => true]);

        // Attempt to create another shareholder with 20% (would total 110%)
        expect(fn () => $this->service->createShareholder([
            'name' => 'Too Much',
            'equity_percentage' => 20,
        ]))->toThrow(InvalidArgumentException::class, 'cannot exceed 100%');
    });

    test('only one office reserve shareholder allowed', function () {
        Shareholder::factory()->officeReserve()->create(['equity_percentage' => 20]);

        expect(fn () => $this->service->createShareholder([
            'name' => 'Another Office',
            'equity_percentage' => 10,
            'is_office_reserve' => true,
        ]))->toThrow(InvalidArgumentException::class, 'Office Reserve entity already exists');
    });

    test('can update shareholder equity percentage', function () {
        $shareholder = Shareholder::factory()->create(['equity_percentage' => 25]);

        $updated = $this->service->updateShareholder($shareholder->id, [
            'equity_percentage' => 30,
        ]);

        expect($updated->equity_percentage)->toBe('30.00');
    });

    test('update equity validation prevents exceeding 100%', function () {
        $shareholder1 = Shareholder::factory()->create(['equity_percentage' => 50]);
        Shareholder::factory()->create(['equity_percentage' => 40]);

        expect(fn () => $this->service->updateShareholder($shareholder1->id, [
            'equity_percentage' => 70, // Would total 110%
        ]))->toThrow(InvalidArgumentException::class, 'cannot exceed 100%');
    });

    test('cannot delete shareholder with distribution history', function () {
        $shareholder = Shareholder::factory()->create();

        // Create a distribution line (simulating history)
        DistributionLine::factory()->create([
            'shareholder_id' => $shareholder->id,
        ]);

        expect(fn () => $this->service->deleteShareholder($shareholder->id))
            ->toThrow(InvalidArgumentException::class, 'Cannot delete shareholder with distribution history');
    });

    test('can delete shareholder without history', function () {
        $shareholder = Shareholder::factory()->create();

        $result = $this->service->deleteShareholder($shareholder->id);

        expect($result)->toBeTrue()
            ->and(Shareholder::find($shareholder->id))->toBeNull(); // Soft deleted
    });

    test('equity validation returns correct status', function () {
        // Create shareholders totaling exactly 100%
        Shareholder::factory()->create(['equity_percentage' => 50, 'is_active' => true]);
        Shareholder::factory()->create(['equity_percentage' => 30, 'is_active' => true]);
        Shareholder::factory()->create(['equity_percentage' => 20, 'is_active' => true]);

        $validation = $this->service->validateEquityTotal();

        expect($validation)->toHaveKey('total', 100.0)
            ->and($validation)->toHaveKey('is_valid', true)
            ->and($validation['message'])->toContain('valid');
    });

    test('equity validation detects invalid total', function () {
        Shareholder::factory()->create(['equity_percentage' => 60, 'is_active' => true]);

        $validation = $this->service->validateEquityTotal();

        expect($validation['is_valid'])->toBeFalse()
            ->and($validation['total'])->toBe(60.0)
            ->and($validation['message'])->toContain('60%');
    });

    test('inactive shareholders not counted in equity total', function () {
        Shareholder::factory()->create(['equity_percentage' => 50, 'is_active' => true]);
        Shareholder::factory()->inactive()->create(['equity_percentage' => 30, 'is_active' => false]);

        $validation = $this->service->validateEquityTotal();

        expect($validation['total'])->toBe(50.0);
    });
});
