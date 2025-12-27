<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['credit', 'debit']);

        return [
            'account_id' => Account::factory(),
            'category_id' => TransactionCategory::factory(),
            'type' => $type,
            'amount' => fake()->numberBetween(1000, 100000), // $10.00 to $1000.00 in cents
            'description' => fake()->optional()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
        ]);
    }

    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'debit',
        ]);
    }
}
