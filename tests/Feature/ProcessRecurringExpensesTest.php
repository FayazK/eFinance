<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Expense;
use App\Models\Transaction;
use App\Services\ExpenseService;
use Carbon\Carbon;

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

describe('Month-end recurrence advances correctly (issue #50)', function () {
    it('lands on month-end without skipping a month and re-anchors to the intended day', function () {
        // A well-funded PKR account so repeated charges never hit the balance guard.
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 10000000, // 100,000.00 PKR
            'is_active' => true,
        ]);

        $template = Expense::factory()->create([
            'account_id' => $account->id,
            'currency_code' => 'PKR',
            'amount' => 500000, // 5,000.00 PKR
            'reporting_amount_pkr' => 500000,
            'is_recurring' => true,
            'is_active' => true,
            'recurrence_frequency' => 'monthly',
            'recurrence_interval' => 1,
            'recurrence_start_date' => '2024-01-31',
            'next_occurrence_date' => '2024-01-31',
            'status' => 'draft',
        ]);

        // Charge Jan 31 → next occurrence must be the last day of February,
        // not March 2 (which would skip February entirely).
        $this->travelTo(Carbon::parse('2024-01-31'));
        app(ExpenseService::class)->processDueRecurringExpenses();
        $template->refresh();
        expect($template->next_occurrence_date->format('Y-m-d'))->toBe('2024-02-29');

        // Charge Feb 29 → next occurrence must re-anchor to March 31,
        // not drift to March 28/29.
        $this->travelTo(Carbon::parse('2024-02-29'));
        app(ExpenseService::class)->processDueRecurringExpenses();
        $template->refresh();
        expect($template->next_occurrence_date->format('Y-m-d'))->toBe('2024-03-31');
    });
});
