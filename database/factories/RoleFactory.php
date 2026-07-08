<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(),
            'description' => null,
            'permissions' => [],
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the role is the super admin (bypasses all permission checks).
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Super Admin',
            'slug' => config('permissions.super_admin_slug'),
            'description' => 'Full system access. Bypasses all permission checks.',
            'permissions' => [],
        ]);
    }
}
