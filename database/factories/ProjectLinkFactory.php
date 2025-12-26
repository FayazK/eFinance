<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectLink>
 */
class ProjectLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(3),
            'url' => fake()->url(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
