<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ExpenseService;
use Illuminate\Console\Command;

class ProcessRecurringExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all due recurring expense templates and create transactions';

    public function __construct(
        private ExpenseService $expenseService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing due recurring expenses...');

        $results = $this->expenseService->processDueRecurringExpenses();

        $this->info("Successfully processed {$results['processed']} recurring expense(s).");

        if (count($results['failed']) > 0) {
            $this->error('Failed to process '.count($results['failed']).' recurring expense(s):');
            foreach ($results['failed'] as $failure) {
                $this->error("  - Expense #{$failure['id']} ({$failure['vendor']}): {$failure['error']}");
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
