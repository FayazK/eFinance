<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $account = Account::inRandomOrder()->first() ?? Account::factory()->create();
        $currency = $account->currency_code;

        return [
            'account_id' => $account->id,
            'category_id' => TransactionCategory::where('type', 'expense')->inRandomOrder()->first()?->id,
            'amount' => fake()->numberBetween(1000, 100000), // 10.00 to 1000.00 in minor units
            'currency_code' => $currency,
            'vendor' => fake()->optional(0.7)->company(),
            'description' => fake()->optional(0.6)->sentence(),
            'expense_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'status' => fake()->randomElement(['draft', 'processed']),
            'is_recurring' => false,
            'is_active' => true,
        ];
    }

    /**
     * Indicate expense is recurring
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurrence_frequency' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
            'recurrence_interval' => 1,
            'recurrence_start_date' => now()->subDays(30),
            'next_occurrence_date' => now()->addDays(30),
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate expense is due today for recurring processing
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurrence_frequency' => 'monthly',
            'next_occurrence_date' => now()->format('Y-m-d'),
            'is_active' => true,
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate expense is international (with exchange rate)
     */
    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'exchange_rate' => fake()->randomFloat(4, 270, 285),
            'reporting_amount_pkr' => (int) ($attributes['amount'] * 278.0),
        ]);
    }

    /**
     * Indicate expense is processed
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
            'transaction_id' => null, // Will be set when transaction is created
        ]);
    }
}
