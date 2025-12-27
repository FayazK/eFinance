<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income Categories
            ['name' => 'Client Payment', 'type' => 'income', 'color' => 'green'],
            ['name' => 'Invoice Payment', 'type' => 'income', 'color' => 'green'],
            ['name' => 'Upwork Payment', 'type' => 'income', 'color' => 'green'],
            ['name' => 'Interest Income', 'type' => 'income', 'color' => 'green'],

            // Expense Categories
            ['name' => 'Salary', 'type' => 'expense', 'color' => 'red'],
            ['name' => 'Rent', 'type' => 'expense', 'color' => 'red'],
            ['name' => 'Office Supplies', 'type' => 'expense', 'color' => 'orange'],
            ['name' => 'Utilities', 'type' => 'expense', 'color' => 'orange'],
            ['name' => 'Software Subscriptions', 'type' => 'expense', 'color' => 'orange'],
            ['name' => 'Marketing', 'type' => 'expense', 'color' => 'orange'],
            ['name' => 'Professional Services', 'type' => 'expense', 'color' => 'orange'],
        ];

        foreach ($categories as $category) {
            TransactionCategory::create($category);
        }
    }
}
