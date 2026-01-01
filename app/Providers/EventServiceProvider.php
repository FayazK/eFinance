<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\PayrollAdjusted;
use App\Events\PayrollGenerated;
use App\Events\PayrollPaid;
use App\Listeners\LogPayrollActivity;
use App\Listeners\SendPayslipEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PayrollGenerated::class => [
            [LogPayrollActivity::class, 'handleGenerated'],
        ],
        PayrollPaid::class => [
            SendPayslipEmail::class,
            [LogPayrollActivity::class, 'handlePaid'],
        ],
        PayrollAdjusted::class => [
            [LogPayrollActivity::class, 'handleAdjusted'],
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
