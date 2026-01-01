<?php

use App\Events\PayrollGenerated;
use App\Events\PayrollPaid;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Payroll Generation', function () {
    test('generates payroll for all active employees', function () {
        Employee::factory()->count(3)->create(['status' => 'active']);
        Employee::factory()->create(['status' => 'terminated']); // Should be excluded

        Event::fake();

        $response = $this->postJson(route('payroll.generate'), [
            'month' => 1,
            'year' => 2026,
        ]);

        $response->assertStatus(201);

        expect(Payroll::count())->toBe(3); // Only active employees
        Event::assertDispatched(PayrollGenerated::class);
    });

    test('prevents duplicate payroll generation', function () {
        $employee = Employee::factory()->create();
        Payroll::factory()->create([
            'employee_id' => $employee->id,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->postJson(route('payroll.generate'), [
            'month' => 1,
            'year' => 2026,
        ]);

        $response->assertStatus(422);
    });

    test('snapshots base salary at generation time', function () {
        $employee = Employee::factory()->create(['base_salary_pkr' => 15000000]);

        $this->postJson(route('payroll.generate'), [
            'month' => 1,
            'year' => 2026,
        ]);

        $payroll = Payroll::first();
        expect($payroll->base_salary)->toBe(15000000);

        // Change employee salary
        $employee->update(['base_salary_pkr' => 20000000]);

        // Payroll should still have old salary
        expect($payroll->fresh()->base_salary)->toBe(15000000);
    });

    test('validates month and year', function () {
        $response = $this->postJson(route('payroll.generate'), [
            'month' => 13, // Invalid
            'year' => 2026,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    });
});

describe('Payroll Adjustments', function () {
    test('updates bonus and deductions', function () {
        $payroll = Payroll::factory()->create([
            'base_salary' => 15000000,
            'status' => 'pending',
        ]);

        $response = $this->putJson(route('payroll.update-adjustments', $payroll->id), [
            'bonus' => 10000, // 10k PKR
            'deductions' => 5000, // 5k PKR
        ]);

        $response->assertStatus(200);

        $payroll->refresh();
        expect($payroll->bonus)->toBe(1000000); // Minor units
        expect($payroll->deductions)->toBe(500000);
        expect($payroll->net_payable)->toBe(15500000); // 155k PKR (150k + 10k - 5k)
    });

    test('prevents editing paid payroll', function () {
        $payroll = Payroll::factory()->paid()->create();

        $response = $this->putJson(route('payroll.update-adjustments', $payroll->id), [
            'bonus' => 10000,
        ]);

        $response->assertStatus(422);
    });

    test('auto-calculates net payable', function () {
        $payroll = Payroll::factory()->create([
            'base_salary' => 10000000, // 100k
            'status' => 'pending',
        ]);

        $this->putJson(route('payroll.update-adjustments', $payroll->id), [
            'bonus' => 20000, // 20k
            'deductions' => 15000, // 15k
        ]);

        expect($payroll->fresh()->net_payable)->toBe(10500000); // 105k
    });
});

describe('Payroll Payment', function () {
    test('rejects payment from non-PKR account', function () {
        $usdAccount = Account::factory()->create(['currency_code' => 'USD']);
        $payroll = Payroll::factory()->create(['status' => 'pending']);

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $usdAccount->id,
            'payroll_ids' => [$payroll->id],
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['account_id']);
    });

    test('hard blocks on insufficient balance', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 5000000, // 50k PKR
        ]);
        $payroll = Payroll::factory()->create([
            'status' => 'pending',
            'base_salary' => 10000000, // 100k PKR (insufficient, net_payable will auto-calculate)
        ]);

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $account->id,
            'payroll_ids' => [$payroll->id],
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['account_id']);
    });

    test('creates transaction and updates balances atomically', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 20000000, // 200k PKR
        ]);
        $payroll = Payroll::factory()->create([
            'status' => 'pending',
            'base_salary' => 10000000, // 100k PKR (net_payable will auto-calculate)
        ]);

        Event::fake();

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $account->id,
            'payroll_ids' => [$payroll->id],
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(200);

        // Check payroll status
        $payroll->refresh();
        expect($payroll->status)->toBe('paid');
        expect($payroll->paid_at)->not->toBeNull();
        expect($payroll->transaction_id)->not->toBeNull();

        // Check account balance
        expect($account->fresh()->current_balance)->toBe(10000000); // 100k remaining

        // Check transaction created
        $transaction = Transaction::find($payroll->transaction_id);
        expect($transaction->type)->toBe('debit');
        expect($transaction->amount)->toBe(10000000);
        expect($transaction->reference_type)->toBe(Payroll::class);
        expect($transaction->reference_id)->toBe($payroll->id);

        Event::assertDispatched(PayrollPaid::class);
    });

    test('pays multiple payrolls in batch', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 50000000, // 500k PKR
        ]);

        $payrolls = Payroll::factory()->count(3)->create([
            'status' => 'pending',
            'base_salary' => 10000000, // 100k each = 300k total (net_payable will auto-calculate)
        ]);

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $account->id,
            'payroll_ids' => $payrolls->pluck('id')->toArray(),
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(200);

        // All payrolls should be paid
        expect(Payroll::where('status', 'paid')->count())->toBe(3);

        // Account balance should reflect all payments
        expect($account->fresh()->current_balance)->toBe(20000000); // 200k remaining
    });

    test('prevents paying already paid payroll', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 50000000,
        ]);
        $paidPayroll = Payroll::factory()->paid()->create();

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $account->id,
            'payroll_ids' => [$paidPayroll->id],
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payroll_ids']);
    });

    test('batch payment is atomic - rolls back on error', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 20000000, // 200k PKR
        ]);

        $payroll1 = Payroll::factory()->create([
            'status' => 'pending',
            'net_payable' => 10000000, // 100k
        ]);
        $payroll2 = Payroll::factory()->paid()->create(); // Already paid - will cause error

        $initialBalance = $account->current_balance;

        $response = $this->postJson(route('payroll.pay'), [
            'account_id' => $account->id,
            'payroll_ids' => [$payroll1->id, $payroll2->id],
            'payment_date' => '2026-01-05',
        ]);

        $response->assertStatus(422);

        // Balance should be unchanged (rollback)
        expect($account->fresh()->current_balance)->toBe($initialBalance);

        // First payroll should still be pending (rollback)
        expect($payroll1->fresh()->status)->toBe('pending');
    });
});

describe('Payroll Retrieval', function () {
    test('retrieves payrolls for specific month', function () {
        Payroll::factory()->create(['month' => 1, 'year' => 2026]);
        Payroll::factory()->create(['month' => 2, 'year' => 2026]);

        $response = $this->get(route('payroll.index', ['month' => 1, 'year' => 2026]));

        $response->assertStatus(200);
    });

    test('shows payroll with employee and transaction', function () {
        $payroll = Payroll::factory()->paid()->create();

        $response = $this->get(route('payroll.show', $payroll));

        $response->assertStatus(200);
    });
});
