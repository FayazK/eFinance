<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark unpaid invoices past their due date as overdue';

    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for overdue invoices...');

        $count = $this->invoiceService->markOverdueInvoices();

        if ($count > 0) {
            $this->info("Marked {$count} invoice(s) as overdue.");
        } else {
            $this->info('No overdue invoices found.');
        }

        return self::SUCCESS;
    }
}
