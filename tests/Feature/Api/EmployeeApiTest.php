<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    // Read role permissions directly in tests — deterministic, no cross-test cache bleed.
    config(['permissions.cache.enabled' => false]);
});

/**
 * Create a user whose role holds exactly the given permissions and return
 * a Bearer auth header for a fresh token.
 *
 * @param  list<string>  $permissions
 * @return array<string, string>
 */
function employeeApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/employees', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/employees')->assertUnauthorized();
    });

    it('returns 403 without employees.read', function () {
        $this->getJson('/api/v1/employees', employeeApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists employees in a paginated envelope', function () {
        Employee::factory()->count(3)->create();

        $this->getJson('/api/v1/employees', employeeApiBearer(['employees.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'name', 'designation', 'email',
                    'base_salary', 'deposit_currency', 'formatted_salary',
                    'status', 'is_active',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('POST /api/v1/employees', function () {
    it('returns 403 without employees.create', function () {
        $this->postJson('/api/v1/employees', [
            'name' => 'Jane Doe',
            'designation' => 'Engineer',
            'email' => 'jane@example.com',
            'joining_date' => '2026-01-01',
            'base_salary' => 50000,
            'deposit_currency' => 'PKR',
        ], employeeApiBearer(['employees.read']))
            ->assertForbidden();
    });

    it('creates an employee and stores the salary in minor units', function () {
        $response = $this->postJson('/api/v1/employees', [
            'name' => 'Jane Doe',
            'designation' => 'Engineer',
            'email' => 'jane@example.com',
            'joining_date' => '2026-01-01',
            'base_salary' => 50000, // major units on input
            'deposit_currency' => 'PKR',
        ], employeeApiBearer(['employees.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'base_salary', 'deposit_currency', 'formatted_salary']])
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.deposit_currency', 'PKR')
            ->assertJsonPath('data.base_salary', 50000); // major units out — no double-convert

        // Persisted in minor units (paisa): 50000 -> 5000000.
        $this->assertDatabaseHas('employees', [
            'id' => $response->json('data.id'),
            'name' => 'Jane Doe',
            'base_salary' => 5000000,
        ]);
    });

    it('returns 422 when required fields are missing', function () {
        $this->postJson('/api/v1/employees', [], employeeApiBearer(['employees.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'designation', 'email', 'joining_date', 'base_salary', 'deposit_currency',
            ]);
    });

    it('returns 422 for a deposit_currency outside PKR,USD', function () {
        $this->postJson('/api/v1/employees', [
            'name' => 'Jane Doe',
            'designation' => 'Engineer',
            'email' => 'jane@example.com',
            'joining_date' => '2026-01-01',
            'base_salary' => 50000,
            'deposit_currency' => 'EUR',
        ], employeeApiBearer(['employees.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['deposit_currency']);
    });
});

describe('id-scoped employee actions', function () {
    it('returns 404 for a missing employee', function () {
        $this->getJson('/api/v1/employees/999999', employeeApiBearer(['employees.read']))
            ->assertNotFound();
    });

    it('shows a single employee with the salary back in major units', function () {
        $employee = Employee::factory()->create(['name' => 'Main Staff', 'base_salary' => 7500000]);

        $this->getJson("/api/v1/employees/{$employee->id}", employeeApiBearer(['employees.read']))
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'base_salary', 'deposit_currency', 'formatted_salary']])
            ->assertJsonPath('data.name', 'Main Staff')
            ->assertJsonPath('data.base_salary', 75000);
    });

    it('updates an employee and stores the salary in minor units', function () {
        $employee = Employee::factory()->create(['base_salary' => 5000000]);

        $this->putJson("/api/v1/employees/{$employee->id}", [
            'name' => 'Renamed Staff',
            'base_salary' => 60000, // major units on input
        ], employeeApiBearer(['employees.update']))
            ->assertOk()
            ->assertJsonPath('data.name', 'Renamed Staff')
            ->assertJsonPath('data.base_salary', 60000);

        // 60000 major -> 6000000 minor.
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Renamed Staff',
            'base_salary' => 6000000,
        ]);
    });

    it('returns 404 when updating a missing employee', function () {
        $this->putJson('/api/v1/employees/999999', [
            'name' => 'Nobody',
        ], employeeApiBearer(['employees.update']))
            ->assertNotFound();
    });

    it('returns 422 for a deposit_currency outside PKR,USD on update', function () {
        $employee = Employee::factory()->create();

        $this->putJson("/api/v1/employees/{$employee->id}", [
            'deposit_currency' => 'EUR',
        ], employeeApiBearer(['employees.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['deposit_currency']);
    });

    it('returns 403 without employees.delete', function () {
        $employee = Employee::factory()->create();

        $this->deleteJson("/api/v1/employees/{$employee->id}", [], employeeApiBearer(['employees.read']))
            ->assertForbidden();
    });

    it('deletes an employee', function () {
        $employee = Employee::factory()->create();

        $this->deleteJson("/api/v1/employees/{$employee->id}", [], employeeApiBearer(['employees.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Employee deleted successfully');

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    });

    it('returns 404 when deleting a missing employee', function () {
        $this->deleteJson('/api/v1/employees/999999', [], employeeApiBearer(['employees.delete']))
            ->assertNotFound();
    });
});
