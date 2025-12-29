<?php

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Transfer Index', function () {
    test('guests are redirected to login', function () {
        $this->get('/dashboard/transfers')->assertRedirect('/login');
    });

    test('authenticated users can visit transfers index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/transfers')->assertOk();
    });
});

describe('Transfer Create', function () {
    test('authenticated users can visit create transfer page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/transfers/create')->assertOk();
    });

    test('same-currency transfer is created successfully', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // $1000.00
        ]);

        $destinationAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 50000, // $500.00
        ]);

        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'source_amount' => 500.00,
            'destination_amount' => 500.00,
            'description' => 'Test same-currency transfer',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Transfer completed successfully');

        // Verify balances updated
        $sourceAccount->refresh();
        $destinationAccount->refresh();

        expect($sourceAccount->current_balance)->toBe(50000); // $500 left
        expect($destinationAccount->current_balance)->toBe(100000); // $1000 total

        // Verify transfer record created
        $transfer = Transfer::first();
        expect((float) $transfer->exchange_rate)->toBe(1.0);
        expect($transfer->fee_transaction_id)->toBeNull(); // No fee for this transfer

        // Verify both transactions created
        $this->assertDatabaseHas('transactions', [
            'account_id' => $sourceAccount->id,
            'type' => 'debit',
            'amount' => 50000,
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $destinationAccount->id,
            'type' => 'credit',
            'amount' => 50000,
        ]);
    });

    test('multi-currency transfer calculates exchange rate correctly', function () {
        $this->actingAs($this->user);

        $usdAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 200000, // $2000.00
        ]);

        $pkrAccount = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 0,
        ]);

        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $usdAccount->id,
            'destination_account_id' => $pkrAccount->id,
            'source_amount' => 1000.00, // $1000
            'destination_amount' => 278000.00, // Rs. 278,000
            'description' => 'USD to PKR transfer',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        // Verify exchange rate calculated
        $transfer = Transfer::first();
        expect((float) $transfer->exchange_rate)->toBe(278.0);
        expect($transfer->fee_transaction_id)->toBeNull(); // No fee for this transfer

        // Verify balances
        $usdAccount->refresh();
        $pkrAccount->refresh();

        expect($usdAccount->current_balance)->toBe(100000); // $1000 left
        expect($pkrAccount->current_balance)->toBe(27800000); // Rs. 278,000
    });

    test('transfer to same account is rejected', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $account->id,
            'destination_account_id' => $account->id,
            'source_amount' => 100.00,
            'destination_amount' => 100.00,
            'date' => '2025-12-27',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['source_account_id']);
    });

    test('transfer requires all required fields', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/transfers', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'source_account_id',
            'destination_account_id',
            'source_amount',
            'destination_amount',
            'date',
        ]);
    });

    test('transfer amounts must be positive', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create();
        $destinationAccount = Account::factory()->create();

        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'source_amount' => -100.00,
            'destination_amount' => 100.00,
            'date' => '2025-12-27',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['source_amount']);
    });

    test('transfer is atomic - rollback on failure', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create([
            'current_balance' => 100000,
        ]);

        $destinationAccount = Account::factory()->create([
            'current_balance' => 50000,
        ]);

        // Force a failure by using invalid data after initial validation
        try {
            $this->postJson('/dashboard/transfers', [
                'source_account_id' => $sourceAccount->id,
                'destination_account_id' => 999999, // Non-existent account
                'source_amount' => 100.00,
                'destination_amount' => 100.00,
                'date' => '2025-12-27',
            ]);
        } catch (\Exception $e) {
            // Expected to fail
        }

        // Verify balances unchanged
        $sourceAccount->refresh();
        $destinationAccount->refresh();

        expect($sourceAccount->current_balance)->toBe(100000);
        expect($destinationAccount->current_balance)->toBe(50000);

        // Verify no transfer created
        expect(Transfer::count())->toBe(0);
    });

    test('same-currency transfer with implicit fee is created successfully', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // $1000.00
        ]);

        $destinationAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 50000, // $500.00
        ]);

        // Send $503, receive $500 → implicit fee of $3
        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'source_amount' => 503.00, // Total sent
            'destination_amount' => 500.00, // Amount received
            'description' => 'Test transfer with implicit fee',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        // Verify 3 transactions created (withdrawal $500, deposit $500, fee $3)
        expect(\App\Models\Transaction::count())->toBe(3);

        // Verify balances: source -= 503, dest += 500
        $sourceAccount->refresh();
        $destinationAccount->refresh();

        expect($sourceAccount->current_balance)->toBe(49700); // $497 left ($1000 - $503)
        expect($destinationAccount->current_balance)->toBe(100000); // $1000 total ($500 + $500)

        // Verify transfer has fee_transaction_id
        $transfer = Transfer::first();
        expect($transfer->fee_transaction_id)->not->toBeNull();

        // Verify fee transaction is $3
        $this->assertDatabaseHas('transactions', [
            'account_id' => $sourceAccount->id,
            'type' => 'debit',
            'amount' => 300, // $3 in cents
        ]);

        // Verify fee transaction has correct category
        $feeTransaction = \App\Models\Transaction::find($transfer->fee_transaction_id);
        expect($feeTransaction->category->name)->toBe('Bank Charges & Fees');
    });

    test('same-currency transfer without fee works as before', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000,
        ]);

        $destinationAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 50000,
        ]);

        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'source_amount' => 500.00,
            'destination_amount' => 500.00,
            'description' => 'Test transfer without fee',
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        // Verify only 2 transactions created (withdrawal and deposit)
        expect(\App\Models\Transaction::count())->toBe(2);

        // Verify transfer has null fee_transaction_id
        $transfer = Transfer::first();
        expect($transfer->fee_transaction_id)->toBeNull();
    });

    test('same-currency negative fee is rejected', function () {
        $this->actingAs($this->user);

        $sourceAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000,
        ]);

        $destinationAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 50000,
        ]);

        // Try to send $500 but receive $503 (would create negative fee)
        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'source_amount' => 500.00,
            'destination_amount' => 503.00, // More than sent
            'date' => '2025-12-27',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['source_amount']);
    });

    test('cross-currency with different amounts calculates exchange rate not fee', function () {
        $this->actingAs($this->user);

        $usdAccount = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 200000, // $2000
        ]);

        $pkrAccount = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 0,
        ]);

        // Cross-currency: different amounts represent exchange rate, not fee
        $response = $this->postJson('/dashboard/transfers', [
            'source_account_id' => $usdAccount->id,
            'destination_account_id' => $pkrAccount->id,
            'source_amount' => 1000.00,
            'destination_amount' => 278000.00,
            'date' => '2025-12-27',
        ]);

        $response->assertCreated();

        // Verify only 2 transactions created (no fee for cross-currency)
        expect(\App\Models\Transaction::count())->toBe(2);

        // Verify transfer has no fee
        $transfer = Transfer::first();
        expect($transfer->fee_transaction_id)->toBeNull();

        // Verify exchange rate calculated
        expect((float) $transfer->exchange_rate)->toBe(278.0);
    });

});

describe('Transfer Data Fetching', function () {
    test('transfers are returned with pagination', function () {
        $this->actingAs($this->user);

        Transfer::factory()->count(20)->create();

        $response = $this->getJson('/dashboard/transfers/data?per_page=10');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page'],
            'links',
        ]);
        $response->assertJsonCount(10, 'data');
    });

    test('transfers can be filtered by account', function () {
        $this->actingAs($this->user);

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        // Create transfers involving account1
        Transfer::factory()->count(3)->create();

        $response = $this->getJson("/dashboard/transfers/data?account_id={$account1->id}");

        $response->assertOk();
    });
});

describe('Transfer Show', function () {
    test('transfer details can be retrieved', function () {
        $this->actingAs($this->user);

        $transfer = Transfer::factory()->create();

        $response = $this->getJson("/dashboard/transfers/{$transfer->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'source_account',
            'destination_account',
            'source_amount',
            'destination_amount',
            'exchange_rate',
        ]);
    });

    test('non-existent transfer returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->getJson('/dashboard/transfers/999999');

        $response->assertNotFound();
    });
});
