<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Payroll;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayrollPaid
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Payroll $payroll
    ) {}
}
