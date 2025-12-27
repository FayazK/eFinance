<?php

use App\Models\TransactionCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Transaction Category Index', function () {
    test('guests are redirected to the login page', function () {
        $this->get('/dashboard/transaction-categories')->assertRedirect('/login');
    });

    test('authenticated users can visit the transaction categories index', function () {
        $this->withoutVite();
        $this->actingAs($this->user);

        $this->get('/dashboard/transaction-categories')->assertOk();
    });
});

describe('Transaction Category Create', function () {
    test('authenticated users can create a transaction category', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/transaction-categories', [
            'name' => 'Client Payment',
            'type' => 'income',
            'color' => 'green',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Category created successfully');

        $this->assertDatabaseHas('transaction_categories', [
            'name' => 'Client Payment',
            'type' => 'income',
            'color' => 'green',
        ]);
    });

    test('category creation requires name and type', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/transaction-categories', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'type']);
    });

    test('category type must be income or expense', function () {
        $this->actingAs($this->user);

        $response = $this->postJson('/dashboard/transaction-categories', [
            'name' => 'Test Category',
            'type' => 'invalid_type',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['type']);
    });
});

describe('Transaction Category Update', function () {
    test('authenticated users can update a transaction category', function () {
        $this->actingAs($this->user);

        $category = TransactionCategory::factory()->create([
            'name' => 'Old Name',
            'type' => 'income',
        ]);

        $response = $this->putJson("/dashboard/transaction-categories/{$category->id}", [
            'name' => 'Updated Name',
            'type' => 'expense',
            'color' => 'red',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Category updated successfully');

        $this->assertDatabaseHas('transaction_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'type' => 'expense',
            'color' => 'red',
        ]);
    });
});

describe('Transaction Category Delete', function () {
    test('authenticated users can delete a transaction category', function () {
        $this->actingAs($this->user);

        $category = TransactionCategory::factory()->create();

        $response = $this->deleteJson("/dashboard/transaction-categories/{$category->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Category deleted successfully');

        $this->assertDatabaseMissing('transaction_categories', ['id' => $category->id]);
    });

    test('deleting non-existent category returns 404', function () {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/dashboard/transaction-categories/99999');

        $response->assertNotFound();
    });
});

describe('Transaction Category Data', function () {
    test('categories are returned via data endpoint', function () {
        $this->actingAs($this->user);

        TransactionCategory::factory()->count(5)->create();

        $response = $this->getJson('/dashboard/transaction-categories/data');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    });

    test('seeded categories exist in database', function () {
        $this->actingAs($this->user);

        // Run the seeder
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\TransactionCategorySeeder']);

        // Verify income categories
        $this->assertDatabaseHas('transaction_categories', [
            'name' => 'Client Payment',
            'type' => 'income',
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'name' => 'Invoice Payment',
            'type' => 'income',
        ]);

        // Verify expense categories
        $this->assertDatabaseHas('transaction_categories', [
            'name' => 'Salary',
            'type' => 'expense',
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'name' => 'Rent',
            'type' => 'expense',
        ]);
    });
});
