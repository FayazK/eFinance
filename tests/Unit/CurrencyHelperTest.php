<?php

declare(strict_types=1);

use App\Helpers\CurrencyHelper;

test('toMinor rounds major units to minor units instead of truncating', function (float|int|string $major, int $expectedMinor) {
    expect(CurrencyHelper::toMinor($major))->toBe($expectedMinor);
})->with([
    // Values whose float product falls just below the integer and used to truncate.
    '1.15 -> 115' => [1.15, 115],
    '19.99 -> 1999' => [19.99, 1999],
    '4.35 -> 435' => [4.35, 435],
    '0.29 -> 29' => [0.29, 29],
    '0.57 -> 57' => [0.57, 57],
    // Whole numbers and zero are unaffected.
    '1000 -> 100000' => [1000, 100000],
    '0 -> 0' => [0, 0],
    // Numeric strings (as form-encoded request values arrive) are coerced, not rejected.
    '"1.15" -> 115' => ['1.15', 115],
    '"19.99" -> 1999' => ['19.99', 1999],
    '"75000" -> 7500000' => ['75000', 7500000],
]);
