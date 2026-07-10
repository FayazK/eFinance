<?php

declare(strict_types=1);

namespace App\Helpers;

class EquityHelper
{
    /**
     * Tolerance (in percentage points) for floating-point equity-total comparisons.
     */
    public const EQUITY_EPSILON = 0.01;

    /**
     * Whether an active-equity total is effectively 100% within tolerance.
     *
     * A cap table total is derived from a SQL SUM and cast to float, so a
     * legitimately-100% table can arrive as e.g. 99.999999999999986 on engines
     * that sum in floating point. Compare with a small epsilon instead of ===.
     */
    public static function isComplete(float $total): bool
    {
        return abs($total - 100.0) < self::EQUITY_EPSILON;
    }
}
