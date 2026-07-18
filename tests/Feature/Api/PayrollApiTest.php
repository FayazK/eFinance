<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Employee;
use App\Models\Payroll;
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
function payrollApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/payroll', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/payroll')->assertUnauthorized();
    });

    it('returns 403 without payroll.read', function () {
        $this->getJson('/api/v1/payroll', payrollApiBearer(['expenses.read']))
            ->assertForbidden();
    });

    it('lists payroll rows in a paginated envelope', function () {
        Payroll::factory()->count(3)->create();

        $this->getJson('/api/v1/payroll', payrollApiBearer(['payroll.read']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'employee_id', 'month', 'year',
                    'base_salary', 'net_payable', 'status',
                ]],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });
});

describe('GET /api/v1/payroll/{id}', function () {
    it('returns 404 for a missing payroll', function () {
        $this->getJson('/api/v1/payroll/999999', payrollApiBearer(['payroll.read']))
            ->assertNotFound();
    });

    it('shows a single payroll with its employee loaded', function () {
        $employee = Employee::factory()->create(['name' => 'Jane Doe']);
        $payroll = Payroll::factory()->create(['employee_id' => $employee->id]);

        $this->getJson("/api/v1/payroll/{$payroll->id}", payrollApiBearer(['payroll.read']))
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'employee_id', 'net_payable', 'employee' => ['id', 'name']]])
            ->assertJsonPath('data.id', $payroll->id)
            ->assertJsonPath('data.employee.name', 'Jane Doe');
    });
});

describe('POST /api/v1/payroll/generate', function () {
    it('returns 403 without payroll.update', function () {
        $this->postJson('/api/v1/payroll/generate', [
            'month' => 3,
            'year' => 2026,
        ], payrollApiBearer(['payroll.read']))
            ->assertForbidden();
    });

    it('generates fully populated payroll rows (guards the #70 hydrate regression)', function () {
        Employee::factory()->count(3)->create(['status' => 'active']);

        $response = $this->postJson('/api/v1/payroll/generate', [
            'month' => 3,
            'year' => 2026,
        ], payrollApiBearer(['payroll.update']))
            ->assertCreated()
            ->assertJsonStructure(['data' => [['id', 'employee_id', 'net_payable', 'employee' => ['id']]]])
            ->assertJsonCount(3, 'data');

        // Every generated row is a real persisted model — non-null id/employee/net_payable,
        // and the eager-loaded employee matches employee_id (the #70 hydrate() misuse
        // returned rows with null id/employee_id and zero net_payable).
        foreach ($response->json('data') as $row) {
            expect($row['id'])->not->toBeNull();
            expect($row['employee_id'])->not->toBeNull();
            expect($row['net_payable'])->not->toBeNull()->toBeGreaterThan(0);
            expect($row['employee']['id'])->toBe($row['employee_id']);
        }

        $this->assertDatabaseCount('payrolls', 3);
    });

    it('returns 422 when payroll for the period already exists', function () {
        Payroll::factory()->create(['month' => 4, 'year' => 2026]);

        $this->postJson('/api/v1/payroll/generate', [
            'month' => 4,
            'year' => 2026,
        ], payrollApiBearer(['payroll.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    });

    it('returns 422 for an invalid month', function () {
        $this->postJson('/api/v1/payroll/generate', [
            'month' => 13,
            'year' => 2026,
        ], payrollApiBearer(['payroll.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['month']);
    });
});

describe('POST /api/v1/payroll/pay', function () {
    it('returns 403 without payroll.update', function () {
        $this->postJson('/api/v1/payroll/pay', [
            'payroll_ids' => [1],
            'payment_date' => '2026-03-01',
        ], payrollApiBearer(['payroll.read']))
            ->assertForbidden();
    });

    it('pays a batch of pending PKR payrolls and posts a ledger transaction', function () {
        $account = Account::factory()->create([
            'currency_code' => 'PKR',
            'current_balance' => 100_000_000, // Rs 1,000,000 in paisa
            'is_active' => true,
        ]);
        $payroll = Payroll::factory()->pkr()->create([
            'status' => 'pending',
            'base_salary' => 1_000_000, // Rs 10,000 in paisa
        ]);

        $this->postJson('/api/v1/payroll/pay', [
            'pkr_account_id' => $account->id,
            'payroll_ids' => [$payroll->id],
            'payment_date' => '2026-03-01',
        ], payrollApiBearer(['payroll.update']))
            ->assertOk()
            ->assertJsonPath('data.0.status', 'paid');

        $this->assertDatabaseHas('payrolls', ['id' => $payroll->id, 'status' => 'paid']);
        $this->assertDatabaseHas('transactions', [
            'reference_type' => Payroll::class,
            'reference_id' => $payroll->id,
        ]);
    });

    it('returns 422 when a payroll in the batch is already paid', function () {
        $payroll = Payroll::factory()->paid()->create();

        $this->postJson('/api/v1/payroll/pay', [
            'payroll_ids' => [$payroll->id],
            'payment_date' => '2026-03-01',
        ], payrollApiBearer(['payroll.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payroll_ids']);
    });
});

describe('PUT /api/v1/payroll/{id}/adjustments', function () {
    it('returns 403 without payroll.update', function () {
        $payroll = Payroll::factory()->create(['status' => 'pending']);

        $this->putJson("/api/v1/payroll/{$payroll->id}/adjustments", [
            'bonus' => 10000,
        ], payrollApiBearer(['payroll.read']))
            ->assertForbidden();
    });

    it('applies adjustments and stores amounts in minor units', function () {
        $payroll = Payroll::factory()->create([
            'status' => 'pending',
            'base_salary' => 15_000_000, // Rs 150,000 in paisa
        ]);

        $this->putJson("/api/v1/payroll/{$payroll->id}/adjustments", [
            'bonus' => 10000, // major units on input
            'deductions' => 5000,
        ], payrollApiBearer(['payroll.update']))
            ->assertOk()
            ->assertJsonPath('data.id', $payroll->id)
            ->assertJsonPath('data.bonus', 10000) // major units out
            ->assertJsonPath('data.deductions', 5000);

        // 10000 major -> 1000000 minor; 5000 -> 500000.
        $this->assertDatabaseHas('payrolls', [
            'id' => $payroll->id,
            'bonus' => 1_000_000,
            'deductions' => 500_000,
        ]);
    });

    it('returns 422 when editing a paid payroll', function () {
        $payroll = Payroll::factory()->paid()->create();

        $this->putJson("/api/v1/payroll/{$payroll->id}/adjustments", [
            'bonus' => 10000,
        ], payrollApiBearer(['payroll.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    });

    it('returns 404 for a missing payroll', function () {
        $this->putJson('/api/v1/payroll/999999/adjustments', [
            'bonus' => 10000,
        ], payrollApiBearer(['payroll.update']))
            ->assertNotFound();
    });
});
