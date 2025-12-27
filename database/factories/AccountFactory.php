<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = ['USD', 'PKR', 'EUR'];
        $types = ['bank', 'wallet', 'cash'];

        return [
            'name' => fake()->words(2, true).' Account',
            'type' => fake()->randomElement($types),
            'currency_code' => fake()->randomElement($currencies),
            'current_balance' => fake()->numberBetween(-100000, 500000), // In cents
            'account_number' => fake()->optional()->iban(),
            'bank_name' => fake()->optional()->company(),
            'is_active' => true,
        ];
    }
}
