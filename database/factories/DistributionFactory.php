<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distribution>
 */
class DistributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $revenue = $this->faker->numberBetween(1000000, 5000000); // PKR in minor units
        $expenses = $this->faker->numberBetween(500000, 3000000);
        $netProfit = $revenue - $expenses;

        return [
            'distribution_number' => 'DIST-'.now()->year.'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'period_start' => $this->faker->dateTimeBetween('-3 months', '-2 months'),
            'period_end' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'total_revenue_pkr' => $revenue,
            'total_expenses_pkr' => $expenses,
            'calculated_net_profit_pkr' => $netProfit,
            'adjusted_net_profit_pkr' => null,
            'distributed_amount_pkr' => 0,
            'processed_at' => null,
            'notes' => $this->faker->optional()->sentence(),
            'adjustment_reason' => null,
        ];
    }

    /**
     * Indicate that the distribution has been processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
            'processed_at' => now(),
            'distributed_amount_pkr' => (int) ($attributes['calculated_net_profit_pkr'] * 0.8), // Assuming 80% distributed, 20% office
        ]);
    }

    /**
     * Indicate that the profit was manually adjusted.
     */
    public function adjusted(int $adjustedAmount, string $reason): static
    {
        return $this->state(fn (array $attributes) => [
            'adjusted_net_profit_pkr' => $adjustedAmount,
            'adjustment_reason' => $reason,
        ]);
    }
}
