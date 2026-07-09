<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Distribution;
use App\Models\Role;
use App\Models\User;

describe('Distribution account balance props', function () {
    beforeEach(function () {
        $this->withoutVite();

        // Rs 1,000,000.50 held in the account (stored as paisa in the column).
        $this->account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 100000050,
            'is_active' => true,
        ]);
    });

    test('create page exposes current_balance in major units', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->get(route('distributions.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/distributions/create')
                ->where('pkrAccounts.0.current_balance', 1000000.5)
            );
    });

    test('show page exposes current_balance in major units', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $distribution = Distribution::factory()->create(['status' => 'draft']);

        $this->get(route('distributions.show', $distribution->id))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/distributions/show')
                ->where('pkrAccounts.0.current_balance', 1000000.5)
            );
    });

    test('current_balance is withheld from users without accounts.read', function () {
        // A role that can reach the distributions pages but cannot read accounts.
        $role = Role::factory()->create([
            'permissions' => ['distributions.read', 'distributions.create'],
        ]);
        $this->actingAs(User::factory()->create(['role_id' => $role->id]));

        $this->get(route('distributions.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('pkrAccounts.0.current_balance', null)
                ->where('pkrAccounts.0.formatted_balance', null)
            );
    });
});
