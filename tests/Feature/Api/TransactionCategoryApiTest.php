<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\TransactionCategory;
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
function transactionCategoryApiBearer(array $permissions): array
{
    $role = Role::factory()->create(['permissions' => $permissions]);
    $user = User::factory()->create(['role_id' => $role->id]);

    return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
}

describe('GET /api/v1/transaction-categories', function () {
    it('returns 401 without a token', function () {
        $this->getJson('/api/v1/transaction-categories')->assertUnauthorized();
    });

    it('returns 403 without transaction_categories.read', function () {
        $this->getJson('/api/v1/transaction-categories', transactionCategoryApiBearer(['accounts.read']))
            ->assertForbidden();
    });

    it('lists all categories in a data-only envelope', function () {
        TransactionCategory::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/transaction-categories', transactionCategoryApiBearer(['transaction_categories.read']))
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'type', 'color']]])
            ->assertJsonCount(3, 'data');

        // Unpaginated: no pagination envelope.
        expect($response->json())->not->toHaveKeys(['links', 'meta']);
    });
});

describe('POST /api/v1/transaction-categories', function () {
    it('returns 403 without transaction_categories.create', function () {
        $this->postJson('/api/v1/transaction-categories', [
            'name' => 'Salary',
            'type' => 'expense',
        ], transactionCategoryApiBearer(['transaction_categories.read']))
            ->assertForbidden();
    });

    it('creates a category', function () {
        $response = $this->postJson('/api/v1/transaction-categories', [
            'name' => 'Client Payment',
            'type' => 'income',
            'color' => 'green',
        ], transactionCategoryApiBearer(['transaction_categories.create']))
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'type', 'color']])
            ->assertJsonPath('data.name', 'Client Payment')
            ->assertJsonPath('data.type', 'income');

        $this->assertDatabaseHas('transaction_categories', [
            'id' => $response->json('data.id'),
            'name' => 'Client Payment',
            'type' => 'income',
        ]);
    });

    it('returns 422 when name and type are missing', function () {
        $this->postJson('/api/v1/transaction-categories', [], transactionCategoryApiBearer(['transaction_categories.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type']);
    });

    it('returns 422 for an invalid type', function () {
        $this->postJson('/api/v1/transaction-categories', [
            'name' => 'Odd',
            'type' => 'liability',
        ], transactionCategoryApiBearer(['transaction_categories.create']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    });
});

describe('PUT /api/v1/transaction-categories/{id}', function () {
    it('returns 403 without transaction_categories.update', function () {
        $category = TransactionCategory::factory()->income()->create();

        $this->putJson("/api/v1/transaction-categories/{$category->id}", [
            'name' => 'Renamed',
            'type' => 'income',
        ], transactionCategoryApiBearer(['transaction_categories.read']))
            ->assertForbidden();
    });

    it('updates a category', function () {
        $category = TransactionCategory::factory()->income()->create(['name' => 'Old']);

        $this->putJson("/api/v1/transaction-categories/{$category->id}", [
            'name' => 'New Name',
            'type' => 'expense',
            'color' => 'red',
        ], transactionCategoryApiBearer(['transaction_categories.update']))
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.type', 'expense');

        $this->assertDatabaseHas('transaction_categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'type' => 'expense',
        ]);
    });

    it('returns 404 for a missing category', function () {
        $this->putJson('/api/v1/transaction-categories/999999', [
            'name' => 'X',
            'type' => 'income',
        ], transactionCategoryApiBearer(['transaction_categories.update']))
            ->assertNotFound();
    });

    it('returns 422 for an invalid type', function () {
        $category = TransactionCategory::factory()->income()->create();

        $this->putJson("/api/v1/transaction-categories/{$category->id}", [
            'name' => 'Still here',
            'type' => 'liability',
        ], transactionCategoryApiBearer(['transaction_categories.update']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    });
});

describe('DELETE /api/v1/transaction-categories/{id}', function () {
    it('returns 403 without transaction_categories.delete', function () {
        $category = TransactionCategory::factory()->create();

        $this->deleteJson("/api/v1/transaction-categories/{$category->id}", [], transactionCategoryApiBearer(['transaction_categories.read']))
            ->assertForbidden();
    });

    it('deletes a category', function () {
        $category = TransactionCategory::factory()->create();

        $this->deleteJson("/api/v1/transaction-categories/{$category->id}", [], transactionCategoryApiBearer(['transaction_categories.delete']))
            ->assertOk()
            ->assertJsonPath('message', 'Transaction category deleted successfully');

        $this->assertDatabaseMissing('transaction_categories', ['id' => $category->id]);
    });

    it('returns 404 for a missing category', function () {
        $this->deleteJson('/api/v1/transaction-categories/999999', [], transactionCategoryApiBearer(['transaction_categories.delete']))
            ->assertNotFound();
    });
});
