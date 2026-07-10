<?php

declare(strict_types=1);

use App\Helpers\EquityHelper;

test('isComplete accepts totals that are 100% within floating-point tolerance', function (float $total) {
    expect(EquityHelper::isComplete($total))->toBeTrue();
})->with([
    'exactly 100' => [100.0],
    // The canonical float drift a naive SUM can produce for 33.33 + 33.33 + 33.34.
    'drifts just below 100' => [99.999999999999986],
    'drifts just above 100' => [100.00000000000001],
]);

test('isComplete rejects totals outside the tolerance', function (float $total) {
    expect(EquityHelper::isComplete($total))->toBeFalse();
})->with([
    'under by 0.01' => [99.99],
    'over by 0.01' => [100.01],
    'clearly short' => [60.0],
]);
