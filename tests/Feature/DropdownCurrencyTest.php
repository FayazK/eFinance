<?php

declare(strict_types=1);

use App\Models\User;
use Nnjeim\World\Models\Currency;

// Issue #114: the world `currencies` table has one row per country, so shared currencies
// (USD, EUR, …) repeat dozens of times and the dropdown could only be searched by name.
// The currencies dropdown source must collapse to one row per ISO code, label rows as
// "CODE - Name", and match the search term against the code as well as the name.
describe('Currencies dropdown source (issue #114)', function () {
    beforeEach(function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $make = fn (array $overrides = []) => Currency::create(array_merge([
            'country_id' => 1,
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'symbol_native' => '$',
            'precision' => 2,
            'symbol_first' => true,
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ], $overrides));

        // Two per-country USD rows (the duplication the world table produces) + one PKR.
        $this->usdCanonical = $make();                 // lower id -> canonical
        $this->usdOther = $make();                     // higher id -> non-canonical
        $this->pkr = $make(['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs']);
    });

    test('collapses duplicate rows to one entry per ISO code with "CODE - Name" labels', function () {
        $names = collect($this->getJson('/dropdown?type=currencies')->assertOk()->json())
            ->pluck('name');

        expect($names)->toContain('USD - US Dollar')
            ->and($names)->toContain('PKR - Pakistani Rupee')
            ->and($names->filter(fn ($n) => str_starts_with($n, 'USD'))->count())->toBe(1);
    });

    test('search matches the ISO code', function () {
        $names = collect($this->getJson('/dropdown?type=currencies&search=USD')->assertOk()->json())
            ->pluck('name');

        expect($names)->toContain('USD - US Dollar')
            ->and($names)->not->toContain('PKR - Pakistani Rupee');
    });

    test('search still matches the currency name', function () {
        $names = collect($this->getJson('/dropdown?type=currencies&search=Pakistani')->assertOk()->json())
            ->pluck('name');

        expect($names)->toContain('PKR - Pakistani Rupee')
            ->and($names)->not->toContain('USD - US Dollar');
    });

    test('preselected non-canonical id resolves and stays deduped', function () {
        $usd = collect($this->getJson('/dropdown?type=currencies&id='.$this->usdOther->id)->assertOk()->json())
            ->where('name', 'USD - US Dollar');

        expect($usd)->toHaveCount(1)
            ->and($usd->first()['id'])->toBe($this->usdOther->id);
    });
});
