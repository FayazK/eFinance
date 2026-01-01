<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descriptions = [
            'Website Development',
            'Mobile App Design',
            'API Integration',
            'Database Optimization',
            'Security Audit',
            'Code Review',
            'Consulting Services',
            'Project Management',
            'UI/UX Design',
            'Quality Assurance Testing',
            'DevOps Setup',
            'Cloud Migration',
        ];

        $units = ['hour', 'day', 'unit', 'item'];
        $unit = $this->faker->randomElement($units);

        $quantity = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->numberBetween(5000, 50000); // $50 to $500 in cents
        $amount = $quantity * $unitPrice;

        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'description' => $this->faker->randomElement($descriptions),
            'quantity' => $quantity,
            'unit' => $unit,
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'sort_order' => 0,
        ];
    }
}
