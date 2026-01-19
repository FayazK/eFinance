<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseSalary = $this->faker->numberBetween(5000000, 50000000); // 50k - 500k PKR

        return [
            'employee_id' => \App\Models\Employee::factory(),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->numberBetween(2024, 2026),
            'base_salary' => $baseSalary,
            'bonus' => 0,
            'deductions' => 0,
            'net_payable' => $baseSalary, // Will be auto-calculated by model
            'status' => 'pending',
            'paid_at' => null,
            'transaction_id' => null,
            'notes' => null,
        ];
    }

    /**
     * Indicate that the payroll has bonus
     */
    public function withBonus(int $amount = 1000000): static
    {
        return $this->state(fn (array $attributes) => [
            'bonus' => $amount,
        ]);
    }

    /**
     * Indicate that the payroll has deductions
     */
    public function withDeductions(int $amount = 500000): static
    {
        return $this->state(fn (array $attributes) => [
            'deductions' => $amount,
        ]);
    }

    /**
     * Indicate that the payroll is paid
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the payroll is for USD deposit
     */
    public function usd(): static
    {
        return $this->state(fn (array $attributes) => [
            'deposit_currency' => 'USD',
        ]);
    }

    /**
     * Indicate that the payroll is for PKR deposit
     */
    public function pkr(): static
    {
        return $this->state(fn (array $attributes) => [
            'deposit_currency' => 'PKR',
        ]);
    }
}
