<?php

declare(strict_types=1);

use App\Models\Distribution;
use App\Models\User;

describe('Distribution update', function () {
    beforeEach(function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->distribution = Distribution::factory()->create([
            'status' => 'draft',
            'notes' => 'Original notes',
        ]);
    });

    test('updates a draft distribution and returns 200', function () {
        $response = $this->putJson("/dashboard/distributions/{$this->distribution->id}", [
            'notes' => 'Updated notes',
        ]);

        $response->assertStatus(200);

        expect($this->distribution->fresh()->notes)->toBe('Updated notes');
    });

    test('rejects an invalid date payload with 422', function () {
        $response = $this->putJson("/dashboard/distributions/{$this->distribution->id}", [
            'period_start' => 'not-a-date',
        ]);

        $response->assertStatus(422)->assertInvalid(['period_start']);
    });

    test('rejects editing a processed distribution with 422', function () {
        $processed = Distribution::factory()->processed()->create(['notes' => 'Locked']);

        $response = $this->putJson("/dashboard/distributions/{$processed->id}", [
            'notes' => 'Trying to edit',
        ]);

        $response->assertStatus(422);

        expect($processed->fresh()->notes)->toBe('Locked');
    });
});
