<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['Planning', 'Active', 'Completed', 'Cancelled'];
        $startDate = fake()->optional()->dateTimeBetween('-1 year', 'now');

        return [
            'name' => fake()->catchPhrase(),
            'description' => fake()->optional()->paragraph(),
            'client_id' => Client::factory(),
            'start_date' => $startDate,
            'completion_date' => $startDate ? fake()->optional()->dateTimeBetween($startDate, '+1 year') : null,
            'status' => fake()->randomElement($statuses),
            'budget' => fake()->optional()->randomFloat(2, 1000, 500000),
            'actual_cost' => fake()->optional()->randomFloat(2, 500, 400000),
        ];
    }
}
