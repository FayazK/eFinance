<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        $sourceAccount = Account::factory()->create(['currency_code' => 'USD']);
        $destinationAccount = Account::factory()->create(['currency_code' => 'PKR']);

        $sourceAmount = 100000; // $1000.00
        $destinationAmount = 27800000; // Rs. 278,000.00
        $exchangeRate = 278.0;

        $withdrawal = Transaction::factory()->create([
            'account_id' => $sourceAccount->id,
            'type' => 'debit',
            'amount' => $sourceAmount,
        ]);

        $deposit = Transaction::factory()->create([
            'account_id' => $destinationAccount->id,
            'type' => 'credit',
            'amount' => $destinationAmount,
        ]);

        return [
            'withdrawal_transaction_id' => $withdrawal->id,
            'deposit_transaction_id' => $deposit->id,
            'exchange_rate' => $exchangeRate,
        ];
    }

    public function sameCurrency(): static
    {
        return $this->state(function (array $attributes) {
            $sourceAccount = Account::factory()->create(['currency_code' => 'USD']);
            $destinationAccount = Account::factory()->create(['currency_code' => 'USD']);

            $amount = 50000; // $500.00

            $withdrawal = Transaction::factory()->create([
                'account_id' => $sourceAccount->id,
                'type' => 'debit',
                'amount' => $amount,
            ]);

            $deposit = Transaction::factory()->create([
                'account_id' => $destinationAccount->id,
                'type' => 'credit',
                'amount' => $amount,
            ]);

            return [
                'withdrawal_transaction_id' => $withdrawal->id,
                'deposit_transaction_id' => $deposit->id,
                'exchange_rate' => 1.0,
            ];
        });
    }
}
