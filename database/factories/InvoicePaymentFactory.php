<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentAmount = $this->faker->numberBetween(10000, 500000); // $100 to $5,000 in cents
        $feePercentage = $this->faker->randomElement([0, 0, 0, 2, 3, 5]); // 70% chance of no fee
        $feeAmount = (int) ($paymentAmount * ($feePercentage / 100));
        $amountReceived = $paymentAmount - $feeAmount;

        return [
            'invoice_id' => Invoice::factory(),
            'account_id' => Account::factory(),
            'income_transaction_id' => null,
            'fee_transaction_id' => null,
            'payment_amount' => $paymentAmount,
            'amount_received' => $amountReceived,
            'fee_amount' => $feeAmount,
            'payment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'notes' => $this->faker->optional()->sentence(),
            'voided_at' => null,
        ];
    }

    /**
     * Payment without any fee
     */
    public function noFee(): static
    {
        return $this->state(fn (array $attributes) => [
            'fee_amount' => 0,
            'amount_received' => $attributes['payment_amount'],
        ]);
    }

    /**
     * Payment with fee
     */
    public function withFee(int $feePercentage = 5): static
    {
        return $this->state(function (array $attributes) use ($feePercentage) {
            $feeAmount = (int) ($attributes['payment_amount'] * ($feePercentage / 100));

            return [
                'fee_amount' => $feeAmount,
                'amount_received' => $attributes['payment_amount'] - $feeAmount,
            ];
        });
    }

    /**
     * Voided payment
     */
    public function voided(): static
    {
        return $this->state(fn (array $attributes) => [
            'voided_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }
}
