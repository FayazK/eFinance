<?php

declare(strict_types=1);

use App\Models\Distribution;
use App\Models\Shareholder;
use App\Models\User;

describe('Distribution show page props', function () {
    beforeEach(function () {
        $this->withoutVite();
    });

    // Regression for #81: the show route passed a bare DistributionResource, which
    // Inertia wraps as { data: {...} }, but show.tsx reads it flat — so the page and the
    // Process modal were unreachable. The prop (and its nested lines/shareholder) must be
    // resolved to flat arrays.
    test('show page exposes the distribution prop unwrapped, including nested lines and shareholder', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $distribution = Distribution::factory()->create(['status' => 'draft']);
        $shareholder = Shareholder::factory()->officeReserve()->create();
        $distribution->lines()->create([
            'shareholder_id' => $shareholder->id,
            'equity_percentage_snapshot' => $shareholder->equity_percentage,
            'allocated_amount_pkr' => 3_000_000, // paisa → Rs 30,000.00 major
            'transaction_id' => null,
        ]);

        $this->get(route('distributions.show', $distribution->id))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/distributions/show')
                // Top-level prop is flat (would be under `distribution.data` if wrapped).
                ->where('distribution.status', 'draft')
                ->where('distribution.is_draft', true)
                // Nested lines resolve to a plain, countable array.
                ->has('distribution.lines', 1)
                // Nested shareholder resolves flat so is_office_reserve / name are readable.
                ->where('distribution.lines.0.shareholder.is_office_reserve', true)
                ->where('distribution.lines.0.shareholder.name', 'Office Reserve')
                // Amount reaches the client in major units.
                ->where('distribution.lines.0.allocated_amount_pkr', fn ($amount) => (float) $amount === 30000.0)
            );
    });
});
