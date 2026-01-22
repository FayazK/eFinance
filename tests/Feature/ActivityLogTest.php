<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed world package data
    $this->artisan('db:seed', ['--class' => 'WorldSeeder']);
});

describe('Activity Log API', function () {
    test('can fetch activities for an invoice', function () {
        $invoice = Invoice::factory()->create();

        // Create some activities manually for testing API
        activity()
            ->performedOn($invoice)
            ->causedBy($this->user)
            ->withProperties(['test' => 'value'])
            ->log('Test activity');

        $response = $this->getJson("/dashboard/activities/Invoice/{$invoice->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'event',
                    'subject_type',
                    'subject_id',
                    'causer',
                    'properties',
                    'changes',
                    'created_at',
                    'created_at_human',
                ],
            ],
        ]);
    });

    test('returns empty array for entity with no activities', function () {
        $invoice = Invoice::factory()->create();

        // Clear any auto-logged activities
        Activity::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->delete();

        $response = $this->getJson("/dashboard/activities/Invoice/{$invoice->id}");

        $response->assertOk();
        $response->assertJson(['data' => []]);
    });

    test('can fetch activities for an expense', function () {
        $account = Account::factory()->create();
        $expense = Expense::factory()->create([
            'account_id' => $account->id,
        ]);

        $response = $this->getJson("/dashboard/activities/Expense/{$expense->id}");

        $response->assertOk();
    });

    test('can fetch activities for an account', function () {
        $account = Account::factory()->create();

        $response = $this->getJson("/dashboard/activities/Account/{$account->id}");

        $response->assertOk();
    });
});

describe('Invoice Activity Logging', function () {
    test('logs activity when invoice is created', function () {
        $invoice = Invoice::factory()->create([
            'status' => 'draft',
        ]);

        $activity = Activity::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->where('event', 'created')
            ->first();

        expect($activity)->not->toBeNull();
        expect($activity->description)->toContain('Invoice created');
        expect($activity->causer_id)->toBe($this->user->id);
    });

    test('logs activity when invoice status is updated', function () {
        $invoice = Invoice::factory()->create([
            'status' => 'draft',
        ]);

        // Clear creation activity to focus on update
        Activity::query()->delete();

        $invoice->update(['status' => 'sent']);

        $activity = Activity::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->where('event', 'updated')
            ->first();

        expect($activity)->not->toBeNull();
        expect($activity->properties['old']['status'] ?? null)->toBe('draft');
        expect($activity->properties['attributes']['status'] ?? null)->toBe('sent');
    });

    test('logs activity when invoice is voided', function () {
        $invoice = Invoice::factory()->sent()->create();

        $response = $this->postJson("/dashboard/invoices/{$invoice->id}/void", [
            'void_reason' => 'Client cancelled the project',
        ]);

        $response->assertOk();

        // Check for manual void activity
        $voidActivity = Activity::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->where('description', 'LIKE', '%voided%')
            ->first();

        expect($voidActivity)->not->toBeNull();
        expect($voidActivity->properties['void_reason'] ?? null)->toBe('Client cancelled the project');
    });
});

describe('Expense Activity Logging', function () {
    test('logs activity when expense is created', function () {
        $account = Account::factory()->create();
        $expense = Expense::factory()->create([
            'account_id' => $account->id,
            'status' => 'draft',
        ]);

        $activity = Activity::where('subject_type', Expense::class)
            ->where('subject_id', $expense->id)
            ->where('event', 'created')
            ->first();

        expect($activity)->not->toBeNull();
        expect($activity->description)->toContain('Expense created');
    });

    test('logs activity when expense status changes', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 10000000,
        ]);

        $expense = Expense::factory()->create([
            'account_id' => $account->id,
            'status' => 'draft',
            'currency_code' => 'PKR',
            'amount' => 50000,
        ]);

        // Clear creation activity
        Activity::query()->delete();

        // Process the expense (returns redirect, not JSON)
        $response = $this->post("/dashboard/expenses/{$expense->id}/process");

        $response->assertRedirect(route('expenses.index'));

        // Check for manual process activity
        $processActivity = Activity::where('subject_type', Expense::class)
            ->where('subject_id', $expense->id)
            ->where('description', 'LIKE', '%processed%')
            ->first();

        expect($processActivity)->not->toBeNull();
    });
});

describe('Account Activity Logging', function () {
    test('logs activity when account is created', function () {
        $account = Account::factory()->create([
            'name' => 'Test Bank Account',
        ]);

        $activity = Activity::where('subject_type', Account::class)
            ->where('subject_id', $account->id)
            ->where('event', 'created')
            ->first();

        expect($activity)->not->toBeNull();
        expect($activity->description)->toContain('Account created');
    });

    test('logs activity when account balance changes', function () {
        $account = Account::factory()->create([
            'current_balance' => 100000,
        ]);

        // Clear creation activity
        Activity::query()->delete();

        $account->update(['current_balance' => 200000]);

        $activity = Activity::where('subject_type', Account::class)
            ->where('subject_id', $account->id)
            ->where('event', 'updated')
            ->first();

        expect($activity)->not->toBeNull();
        expect($activity->properties['old']['current_balance'] ?? null)->toBe(100000);
        expect($activity->properties['attributes']['current_balance'] ?? null)->toBe(200000);
    });
});

describe('Activity Resource Formatting', function () {
    test('formats amount fields correctly', function () {
        $invoice = Invoice::factory()->create([
            'total_amount' => 500000, // $5,000 in cents
        ]);

        // Clear and create a controlled activity
        Activity::query()->delete();

        $invoice->update(['total_amount' => 750000]);

        $response = $this->getJson("/dashboard/activities/Invoice/{$invoice->id}");

        $response->assertOk();

        $data = $response->json('data');
        expect($data)->toHaveCount(1);

        // Check that amounts are formatted (divided by 100)
        $changes = $data[0]['changes'] ?? [];
        $totalAmountChange = collect($changes)->firstWhere('field', 'Total Amount');

        expect($totalAmountChange)->not->toBeNull();
        expect($totalAmountChange['old'])->toBe('5,000.00');
        expect($totalAmountChange['new'])->toBe('7,500.00');
    });

    test('includes causer information', function () {
        $invoice = Invoice::factory()->create();

        $response = $this->getJson("/dashboard/activities/Invoice/{$invoice->id}");

        $response->assertOk();

        $data = $response->json('data');
        expect($data)->not->toBeEmpty();
        expect($data[0]['causer']['id'])->toBe($this->user->id);
        expect($data[0]['causer']['name'])->toBe($this->user->name);
    });
});
