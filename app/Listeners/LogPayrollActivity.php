<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PayrollAdjusted;
use App\Events\PayrollGenerated;
use App\Events\PayrollPaid;
use Illuminate\Support\Facades\Log;

class LogPayrollActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle payroll generated event
     */
    public function handleGenerated(PayrollGenerated $event): void
    {
        Log::info('Payroll generated', [
            'month' => $event->month,
            'year' => $event->year,
            'count' => $event->payrolls->count(),
        ]);
    }

    /**
     * Handle payroll paid event
     */
    public function handlePaid(PayrollPaid $event): void
    {
        Log::info('Payroll paid', [
            'payroll_id' => $event->payroll->id,
            'employee_id' => $event->payroll->employee_id,
            'amount' => $event->payroll->net_payable,
        ]);
    }

    /**
     * Handle payroll adjusted event
     */
    public function handleAdjusted(PayrollAdjusted $event): void
    {
        Log::info('Payroll adjusted', [
            'payroll_id' => $event->payroll->id,
            'employee_id' => $event->payroll->employee_id,
            'bonus' => $event->payroll->bonus,
            'deductions' => $event->payroll->deductions,
        ]);
    }
}
