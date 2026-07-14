<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;

describe('Payroll show page props', function () {
    beforeEach(function () {
        $this->withoutVite();
    });

    // Regression for #87: PayrollController@show passed a bare PayrollResource (Inertia wraps as
    // { data: {...} }). The prop — and its nested `employee` — must resolve flat.
    test('show page exposes the payroll prop unwrapped, including nested employee', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $employee = Employee::factory()->create(['name' => 'Jane Doe']);
        $payroll = Payroll::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        $this->get(route('payroll.show', $payroll))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                // Assert the built show.tsx component resolves on disk.
                ->component('dashboard/payroll/show')
                // Top-level prop is flat (would be under `payroll.data` if wrapped).
                ->where('payroll.status', 'pending')
                // Nested single `employee` resolves flat.
                ->where('payroll.employee.name', 'Jane Doe')
            );
    });
});
