<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Exchange Rates
    |--------------------------------------------------------------------------
    |
    | Default exchange rates used as fallback when no recent transaction
    | rates are available. These are approximate rates and should be
    | manually updated or overridden by users when creating expenses.
    |
    */

    'default_rates' => [
        'USD' => 278.0,  // US Dollar to PKR
        'EUR' => 305.0,  // Euro to PKR
        'GBP' => 355.0,  // British Pound to PKR
        'AED' => 75.0,   // UAE Dirham to PKR
        'PKR' => 1.0,    // Pakistani Rupee (base)
    ],
];
