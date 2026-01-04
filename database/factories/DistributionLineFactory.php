<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Distribution;
use App\Models\Shareholder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DistributionLine>
 */
class DistributionLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'distribution_id' => Distribution::factory(),
            'shareholder_id' => Shareholder::factory(),
            'equity_percentage_snapshot' => $this->faker->randomFloat(2, 10, 50),
            'allocated_amount_pkr' => $this->faker->numberBetween(100000, 1000000),
            'transaction_id' => null,
        ];
    }
}
