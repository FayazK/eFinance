<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_transaction_id',
        'deposit_transaction_id',
        'fee_transaction_id',
        'exchange_rate',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:4',
        ];
    }

    /**
     * Get the withdrawal transaction (money leaving source account)
     */
    public function withdrawalTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'withdrawal_transaction_id');
    }

    /**
     * Get the deposit transaction (money entering destination account)
     */
    public function depositTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'deposit_transaction_id');
    }

    /**
     * Get the fee transaction (optional transfer fee from source account)
     */
    public function feeTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'fee_transaction_id');
    }

    /**
     * Get source account via withdrawal transaction
     */
    public function getSourceAccountAttribute(): ?Account
    {
        return $this->withdrawalTransaction?->account;
    }

    /**
     * Get destination account via deposit transaction
     */
    public function getDestinationAccountAttribute(): ?Account
    {
        return $this->depositTransaction?->account;
    }

    /**
     * Get formatted exchange rate for display
     */
    public function getFormattedExchangeRateAttribute(): string
    {
        return number_format((float) $this->exchange_rate, 4);
    }

    /**
     * Get fee amount in major units (or 0 if no fee)
     */
    public function getFeeAmountAttribute(): float
    {
        return $this->feeTransaction ? $this->feeTransaction->amount / 100 : 0.0;
    }

    /**
     * Get formatted fee for display (or empty string if no fee)
     */
    public function getFormattedFeeAttribute(): string
    {
        if (! $this->feeTransaction) {
            return '';
        }

        return $this->feeTransaction->formatted_amount;
    }
}
