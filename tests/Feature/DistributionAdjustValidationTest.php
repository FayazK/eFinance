<?php

declare(strict_types=1);

use App\Models\Distribution;
use App\Models\Shareholder;
use App\Models\User;

describe('Distribution adjust-profit validation', function () {
    beforeEach(function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        // Shareholders totaling 100% so lines can be recreated on a valid adjust.
        Shareholder::factory()->create(['equity_percentage' => 40, 'name' => 'Partner A']);
        Shareholder::factory()->create(['equity_percentage' => 30, 'name' => 'Partner B']);
        Shareholder::factory()->create(['equity_percentage' => 20, 'name' => 'Partner C']);
        Shareholder::factory()->officeReserve()->create(['equity_percentage' => 10, 'name' => 'Office Reserve']);

        $this->distribution = Distribution::factory()->create([
            'status' => 'draft',
            'adjusted_net_profit_pkr' => null,
        ]);
    });

    test('rejects a negative adjusted_amount with 422 and stores nothing', function () {
        $response = $this->putJson("/dashboard/distributions/{$this->distribution->id}/adjust-profit", [
            'adjusted_amount' => -500000,
            'reason' => 'Attempted negative adjustment',
        ]);

        $response->assertStatus(422)->assertInvalid(['adjusted_amount']);

        expect($this->distribution->fresh()->adjusted_net_profit_pkr)->toBeNull();
    });

    test('rejects a fractional adjusted_amount with 422 instead of a 500 TypeError', function () {
        $response = $this->putJson("/dashboard/distributions/{$this->distribution->id}/adjust-profit", [
            'adjusted_amount' => 100.50,
            'reason' => 'Attempted fractional adjustment',
        ]);

        $response->assertStatus(422)->assertInvalid(['adjusted_amount']);

        expect($this->distribution->fresh()->adjusted_net_profit_pkr)->toBeNull();
    });

    test('accepts a valid non-negative integer adjusted_amount (paisa)', function () {
        $response = $this->putJson("/dashboard/distributions/{$this->distribution->id}/adjust-profit", [
            'adjusted_amount' => 5000000, // Rs 50,000 in paisa
            'reason' => 'Holding back reserves',
        ]);

        $response->assertStatus(200);

        expect($this->distribution->fresh()->adjusted_net_profit_pkr)->toBe(5000000);
    });
});
