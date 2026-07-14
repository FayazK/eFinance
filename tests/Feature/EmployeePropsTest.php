<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;

describe('Employee show & edit page props', function () {
    beforeEach(function () {
        $this->withoutVite();
    });

    // Regression for #87: EmployeeController passed bare EmployeeResources (Inertia wraps as
    // { data: {...} }). The prop — and its nested `payrolls` collection — must resolve flat.
    test('show page exposes the employee prop unwrapped, including nested payrolls', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $employee = Employee::factory()->create(['name' => 'Jane Doe']);
        Payroll::factory()->count(2)->create(['employee_id' => $employee->id]);

        $this->get(route('employees.show', $employee))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                // Assert the built show.tsx component resolves on disk.
                ->component('dashboard/employees/show')
                // Top-level prop is flat (would be under `employee.data` if wrapped).
                ->where('employee.name', 'Jane Doe')
                // Nested `payrolls` resolves to a plain, countable array.
                ->has('employee.payrolls', 2)
            );
    });

    test('edit page exposes the employee prop unwrapped', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $employee = Employee::factory()->create(['name' => 'Jane Doe']);

        $this->get(route('employees.edit', $employee))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                // Assert the built edit.tsx component resolves on disk.
                ->component('dashboard/employees/edit')
                ->where('employee.name', 'Jane Doe')
            );
    });
});
