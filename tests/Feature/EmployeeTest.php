<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->superAdmin()->create();
    $this->actingAs($this->user);
});

describe('Employee Creation', function () {
    test('creates employee successfully', function () {
        $data = [
            'name' => 'John Doe',
            'designation' => 'Software Engineer',
            'email' => 'john@example.com',
            'joining_date' => '2026-01-01',
            'base_salary' => 150000, // Major units
            'deposit_currency' => 'PKR',
            'iban' => 'PK36MEZN0003090107800014',
            'bank_name' => 'Meezan Bank',
        ];

        $response = $this->postJson(route('employees.store'), $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Employee created successfully']);

        $this->assertDatabaseHas('employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'base_salary' => 15000000, // Minor units
        ]);
    });

    test('validates required fields', function () {
        $response = $this->postJson(route('employees.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'designation', 'email', 'joining_date', 'base_salary']);
    });

    test('validates unique email', function () {
        Employee::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson(route('employees.store'), [
            'name' => 'Jane Doe',
            'designation' => 'Designer',
            'email' => 'existing@example.com',
            'joining_date' => '2026-01-01',
            'base_salary' => 100000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });
});

describe('Employee Update', function () {
    test('updates employee successfully', function () {
        $employee = Employee::factory()->create([
            'base_salary' => 10000000, // 100k PKR
        ]);

        $response = $this->putJson(route('employees.update', $employee), [
            'name' => 'Updated Name',
            'base_salary' => 200000, // Update to 200k PKR
        ]);

        $response->assertStatus(200);

        expect($employee->fresh()->name)->toBe('Updated Name');
        expect($employee->fresh()->base_salary)->toBe(20000000);
    });

    test('can terminate employee', function () {
        $employee = Employee::factory()->create();

        $response = $this->putJson(route('employees.update', $employee), [
            'status' => 'terminated',
            'termination_date' => '2026-01-15',
        ]);

        $response->assertStatus(200);

        $employee->refresh();
        expect($employee->status)->toBe('terminated');
        expect($employee->termination_date->format('Y-m-d'))->toBe('2026-01-15');
    });
});

describe('Employee Retrieval', function () {
    test('lists employees', function () {
        Employee::factory()->count(3)->create();

        $response = $this->get(route('employees.index'));

        $response->assertStatus(200);
    });

    test('shows employee with payroll history', function () {
        $this->withoutVite();

        $employee = Employee::factory()->create();

        $response = $this->get(route('employees.show', $employee));

        $response->assertStatus(200);
    });
});

describe('Employee Deletion', function () {
    test('soft deletes employee', function () {
        $employee = Employee::factory()->create();

        $response = $this->deleteJson(route('employees.destroy', $employee->id));

        $response->assertStatus(200);

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    });
});

describe('Employee Sorting', function () {
    test('sorts by salary using the real base_salary column', function () {
        $low = Employee::factory()->create(['base_salary' => 10000000]);  // 100k PKR
        $mid = Employee::factory()->create(['base_salary' => 20000000]);  // 200k PKR
        $high = Employee::factory()->create(['base_salary' => 30000000]); // 300k PKR

        $response = $this->getJson(route('employees.data', [
            'sort_by' => 'base_salary',
            'sort_direction' => 'asc',
        ]));

        $response->assertStatus(200);

        $ids = array_column($response->json('data'), 'id');
        expect($ids)->toBe([$low->id, $mid->id, $high->id]);
    });

    // The Ant Design table sends the column's dataIndex ('formatted_salary', a computed
    // accessor) as sort_by. On MySQL an unwhitelisted orderBy on that non-existent column
    // 500s. SQLite silently ignores unknown ORDER BY identifiers, so we assert the real
    // guarantee driver-independently: a non-allowlisted sort_by never reaches the SQL.
    test('ignores a non-allowlisted sort column instead of ordering by it', function () {
        Employee::factory()->count(3)->create();

        DB::enableQueryLog();

        $response = $this->getJson(route('employees.data', [
            'sort_by' => 'formatted_salary',
            'sort_direction' => 'asc',
        ]));

        $response->assertStatus(200);

        $sql = collect(DB::getQueryLog())->pluck('query')->implode(' ');
        expect($sql)->not->toContain('formatted_salary');
    });
});
