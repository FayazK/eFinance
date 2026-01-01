<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = (clone $issueDate)->modify('+30 days');

        $subtotal = $this->faker->numberBetween(10000, 1000000); // $100 to $10,000 in cents
        $taxRate = $this->faker->randomElement([0, 5, 10, 15]); // 0%, 5%, 10%, or 15%
        $taxAmount = (int) ($subtotal * ($taxRate / 100));
        $totalAmount = $subtotal + $taxAmount;

        return [
            'invoice_number' => 'INV-' . now()->format('Y') . '-' . str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'draft',
            'client_id' => \App\Models\Client::factory(),
            'project_id' => null,
            'currency_code' => $this->faker->randomElement(['USD', 'PKR']),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'balance_due' => $totalAmount,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'paid_at' => null,
            'sent_at' => null,
            'voided_at' => null,
            'notes' => $this->faker->optional()->sentence(),
            'terms' => $this->faker->optional()->paragraph(),
            'client_notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Invoice with sent status
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }

    /**
     * Invoice with partial payment
     */
    public function partial(): static
    {
        return $this->state(function (array $attributes) {
            $partialAmount = (int) ($attributes['total_amount'] * $this->faker->randomFloat(2, 0.2, 0.8));

            return [
                'status' => 'partial',
                'amount_paid' => $partialAmount,
                'balance_due' => $attributes['total_amount'] - $partialAmount,
                'sent_at' => now()->subDays($this->faker->numberBetween(5, 60)),
            ];
        });
    }

    /**
     * Fully paid invoice
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'amount_paid' => $attributes['total_amount'],
            'balance_due' => 0,
            'paid_at' => now()->subDays($this->faker->numberBetween(1, 90)),
            'sent_at' => now()->subDays($this->faker->numberBetween(30, 120)),
        ]);
    }

    /**
     * Overdue invoice
     */
    public function overdue(): static
    {
        $daysOverdue = $this->faker->numberBetween(1, 60);

        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'issue_date' => now()->subDays(30 + $daysOverdue),
            'due_date' => now()->subDays($daysOverdue),
            'sent_at' => now()->subDays(35 + $daysOverdue),
        ]);
    }

    /**
     * Voided invoice
     */
    public function voided(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'void',
            'voided_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }
}
