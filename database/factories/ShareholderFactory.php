<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shareholder>
 */
class ShareholderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'equity_percentage' => $this->faker->randomFloat(2, 1, 50),
            'is_office_reserve' => false,
            'is_active' => true,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the shareholder is the office reserve.
     */
    public function officeReserve(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Office Reserve',
            'email' => null,
            'is_office_reserve' => true,
        ]);
    }

    /**
     * Indicate that the shareholder is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
