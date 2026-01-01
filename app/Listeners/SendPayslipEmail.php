<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PayrollPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPayslipEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PayrollPaid $event): void
    {
        // TODO: Implement payslip PDF generation and email sending
        // 1. Generate PDF using spatie/laravel-pdf
        // 2. Send email with PDF attachment to employee
        // 3. This is queued for non-blocking execution

        Log::info('Payslip email placeholder', [
            'payroll_id' => $event->payroll->id,
            'employee' => $event->payroll->employee->name,
        ]);
    }
}
