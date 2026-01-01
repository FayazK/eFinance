<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayrollGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly int $month,
        public readonly int $year,
        public readonly Collection $payrolls
    ) {}
}
