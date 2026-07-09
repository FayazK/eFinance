<?php

use App\Models\Account;
use App\Models\User;
use App\Services\AccountService;

beforeEach(function () {
    $this->user = User::factory()->superAdmin()->create();
});

describe('Account Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/accounts')->assertRedirect('/login');
    });

    test('authenticated users can visit the accounts index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/accounts')->assertOk();
    });

    test('accounts index displays net worth calculation', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        // Create accounts with different balances
        Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 100000, // $1000.00
        ]);
        Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 50000, // PKR 500.00
        ]);

        $response = $this->get('/dashboard/accounts');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('netWorth')
            ->has('accounts.data', 2)
        );
    });
});

describe('Total Net Worth conversion (issue #51)', function () {
    test('converts every configured currency to PKR, not just PKR and USD', function () {
        // Balances all 1,000.00 in major units (minor units in the DB).
        // config('currency.default_rates'): PKR 1, USD 278, EUR 305, GBP 355, AED 75.
        Account::factory()->create(['currency_code' => 'PKR', 'current_balance' => 100000]);
        Account::factory()->create(['currency_code' => 'USD', 'current_balance' => 100000]);
        Account::factory()->create(['currency_code' => 'EUR', 'current_balance' => 100000]);
        Account::factory()->create(['currency_code' => 'GBP', 'current_balance' => 100000]);
        Account::factory()->create(['currency_code' => 'AED', 'current_balance' => 100000]);

        $netWorth = app(AccountService::class)->calculateTotalNetWorth();

        // 1,000 + 278,000 + 305,000 + 355,000 + 75,000 = 1,014,000 PKR
        expect($netWorth['total_pkr'])->toBe(1014000.0);

        // Each non-USD foreign currency must carry a correct, non-zero PKR value.
        $byCode = collect($netWorth['currency_breakdown'])->keyBy('currency_code');
        expect($byCode['EUR']['pkr_value'])->toBe(305000.0)
            ->and($byCode['GBP']['pkr_value'])->toBe(355000.0)
            ->and($byCode['AED']['pkr_value'])->toBe(75000.0);
    });
});

describe('Account Create', function () {
    test('authenticated users can visit the create account page', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/accounts/create')->assertOk();
    });

    test('authenticated users can create an account', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/accounts', [
            'name' => 'Payoneer USD Account',
            'type' => 'wallet',
            'currency_code' => 'USD',
            'current_balance' => 1000.50, // Major units (dollars)
            'account_number' => '1234567890',
            'bank_name' => 'Payoneer',
            'is_active' => true,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Account created successfully');

        // Verify balance is stored as cents (minor units)
        $this->assertDatabaseHas('accounts', [
            'name' => 'Payoneer USD Account',
            'type' => 'wallet',
            'currency_code' => 'USD',
            'current_balance' => 100050, // 1000.50 * 100 = 100050 cents
            'account_number' => '1234567890',
            'bank_name' => 'Payoneer',
            'is_active' => true,
        ]);
    });

    test('account creation rounds a fractional balance to the correct minor units (issue #52)', function (float $major, int $expectedMinor) {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/accounts', [
            'name' => 'Rounding Account',
            'type' => 'bank',
            'currency_code' => 'USD',
            'current_balance' => $major, // Major units
        ]);

        $response->assertCreated();

        // Old code did (int) ($major * 100), which truncated (e.g. 1.15 -> 114).
        $this->assertDatabaseHas('accounts', [
            'name' => 'Rounding Account',
            'current_balance' => $expectedMinor,
        ]);
    })->with([
        '1.15 -> 115' => [1.15, 115],
        '19.99 -> 1999' => [19.99, 1999],
        '4.35 -> 435' => [4.35, 435],
        '0.29 -> 29' => [0.29, 29],
    ]);

    test('account creation requires name, type, and currency', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/accounts', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'type', 'currency_code']);
    });

    test('account creation with zero balance works', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/accounts', [
            'name' => 'New Account',
            'type' => 'bank',
            'currency_code' => 'USD',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('accounts', [
            'name' => 'New Account',
            'current_balance' => 0,
        ]);
    });

    test('account type must be valid', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/accounts', [
            'name' => 'Test Account',
            'type' => 'invalid_type',
            'currency_code' => 'USD',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['type']);
    });
});

describe('Account Update', function () {
    test('authenticated users can update an account', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create([
            'current_balance' => 50000, // $500.00
        ]);

        $response = $this->putJson("/dashboard/accounts/{$account->id}", [
            'name' => 'Updated Account Name',
            'type' => 'wallet',
            'currency_code' => 'USD',
            'current_balance' => 750.25, // Major units
            'is_active' => false,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Account updated successfully');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Account Name',
            'current_balance' => 75025, // 750.25 * 100
            'is_active' => false,
        ]);
    });

    test('balance conversion from major to minor units', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        // Update with a decimal balance
        $response = $this->putJson("/dashboard/accounts/{$account->id}", [
            'name' => $account->name,
            'type' => $account->type,
            'currency_code' => $account->currency_code,
            'current_balance' => 123.45,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'current_balance' => 12345, // Stored as cents
        ]);
    });
});

describe('Account Delete', function () {
    test('authenticated users can delete an account', function () {
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        $response = $this->deleteJson("/dashboard/accounts/{$account->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Account deleted successfully');

        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    });

    test('deleting non-existent account returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/dashboard/accounts/99999');

        $response->assertNotFound();
    });
});

describe('Account Show', function () {
    test('authenticated users can view an account', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $account = Account::factory()->create();

        $response = $this->get("/dashboard/accounts/{$account->id}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->has('account'));
    });
});

describe('Account Accessors', function () {
    test('formatted_balance accessor returns correct format', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => 125050, // $1250.50
        ]);

        expect($account->formatted_balance)->toContain('$');
        expect($account->formatted_balance)->toContain('1,250.50');
    });

    test('balance_in_major_units accessor converts cents to dollars', function () {
        $account = Account::factory()->create([
            'current_balance' => 123456, // $1234.56
        ]);

        expect($account->balance_in_major_units)->toBe(1234.56);
    });

    test('negative balance is formatted correctly', function () {
        $account = Account::factory()->create([
            'currency_code' => 'USD',
            'current_balance' => -50000, // -$500.00
        ]);

        expect($account->formatted_balance)->toContain('-');
        expect($account->balance_in_major_units)->toBe(-500.0);
    });
});
