<?php

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Seed the minimal reference data (one country + currency, plus a state/city)
 * that the Client/Invoice/Account factories require — instead of the full
 * ~155k-row nnjeim/world dataset.
 */
function seedMinimalWorld(): void
{
    DB::table('countries')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test Country',
        'iso2' => 'TC',
        'iso3' => 'TST',
        'phone_code' => '+1',
        'region' => 'Test',
        'subregion' => 'Test',
    ]);

    DB::table('states')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test State',
        'country_id' => 1,
        'country_code' => 'TC',
    ]);

    DB::table('cities')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test City',
        'country_id' => 1,
        'state_id' => 1,
        'country_code' => 'TC',
    ]);

    DB::table('currencies')->insertOrIgnore([
        'id' => 1,
        'name' => 'Test Dollar',
        'code' => 'TSD',
        'country_id' => 1,
        'precision' => 2,
        'symbol' => '$',
        'symbol_native' => '$',
        'symbol_first' => true,
        'decimal_mark' => '.',
        'thousands_separator' => ',',
    ]);
}
