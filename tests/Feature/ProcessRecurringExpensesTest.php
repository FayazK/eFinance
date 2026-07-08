<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use App\Services\ExpenseService;

// No actingAs() here on purpose: the scheduled command runs without an
// authenticated user, which is exactly the context that triggers issue #43.
beforeEach(function () {
    $this->account = Account::factory()->create([
        'name' => 'Office Fund',
        'currency_code' => 'PKR',
        'current_balance' => 5000, // 50.00 PKR — deliberately underfunded
        'is_active' => true,
    ]);
});

describe('Recurring expense processing with insufficient balance (no auth)', function () {
    it('records a clean insufficient-balance failure instead of a fatal error', function () {
        $template = Expense::factory()->dueToday()->create([
            'account_id' => $this->account->id,
            'amount' => 500000, // 5000.00 PKR — far above the account balance
            'currency_code' => 'PKR',
            'reporting_amount_pkr' => 500000,
            'vendor' => 'Landlord',
        ]);

        $result = app(ExpenseService::class)->processDueRecurringExpenses();

        expect($result['processed'])->toBe(0)
            ->and($result['failed'])->toHaveCount(1)
            ->and($result['failed'][0]['error'])->toBe('Insufficient account balance to process this expense.');

        // Nothing was charged and no rows leaked from the rolled-back transaction.
        $this->account->refresh();
        expect($this->account->current_balance)->toBe(5000)
            ->and(Transaction::count())->toBe(0)
            ->and(Expense::where('id', '!=', $template->id)->count())->toBe(0);
    });

    it('runs the console command to completion without throwing a fatal error', function () {
        Expense::factory()->dueToday()->create([
            'account_id' => $this->account->id,
            'amount' => 500000,
            'currency_code' => 'PKR',
            'reporting_amount_pkr' => 500000,
            'vendor' => 'Landlord',
        ]);

        $this->artisan('expenses:process-recurring')
            ->expectsOutputToContain('Insufficient account balance')
            ->assertFailed();
    });
});
