<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
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
            'designation' => $this->faker->randomElement([
                'Software Engineer',
                'Senior Developer',
                'Project Manager',
                'Designer',
                'Marketing Manager',
                'HR Manager',
                'Accountant',
            ]),
            'email' => $this->faker->unique()->safeEmail(),
            'joining_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'base_salary_pkr' => $this->faker->numberBetween(5000000, 50000000), // 50k - 500k PKR in paisa
            'iban' => 'PK'.$this->faker->numerify('##MEZN################'),
            'bank_name' => $this->faker->randomElement(['Meezan Bank', 'HBL', 'UBL', 'Allied Bank']),
            'status' => 'active',
            'termination_date' => null,
        ];
    }

    /**
     * Indicate that the employee is terminated
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
            'termination_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
