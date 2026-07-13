<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;

describe('Account show & edit page props', function () {
    beforeEach(function () {
        $this->withoutVite();
    });

    // Regression for #87: AccountController passed bare AccountResources; Inertia wraps them as
    // { data: {...} }. After ->resolve() the pages receive the prop flat (no `.data` unwrap).
    test('show page exposes the account prop unwrapped', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $account = Account::factory()->create(['name' => 'Office PKR']);

        $this->get(route('accounts.show', $account))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/accounts/show')
                // Top-level prop is flat (would be under `account.data` if wrapped).
                ->where('account.name', 'Office PKR')
            );
    });

    test('edit page exposes the account prop unwrapped', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $account = Account::factory()->create(['name' => 'Office PKR']);

        $this->get(route('accounts.edit', $account))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/accounts/edit')
                ->where('account.name', 'Office PKR')
            );
    });
});
