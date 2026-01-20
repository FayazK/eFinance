<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'account_id',
        'income_transaction_id',
        'fee_transaction_id',
        'payment_amount',
        'amount_received',
        'fee_amount',
        'payment_date',
        'notes',
        'voided_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_amount' => 'integer',
            'amount_received' => 'integer',
            'fee_amount' => 'integer',
            'payment_date' => 'date',
            'voided_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function incomeTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'income_transaction_id');
    }

    public function feeTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'fee_transaction_id');
    }

    public function getFormattedPaymentAmountAttribute(): string
    {
        $currencyCode = $this->invoice?->currency_code ?? $this->account?->currency_code ?? 'USD';

        return CurrencyHelper::format($this->payment_amount / 100, $currencyCode);
    }

    public function getFormattedAmountReceivedAttribute(): string
    {
        $currencyCode = $this->account?->currency_code ?? 'USD';

        return CurrencyHelper::format($this->amount_received / 100, $currencyCode);
    }

    public function getFormattedFeeAttribute(): string
    {
        if ($this->fee_amount === 0) {
            return '';
        }

        $currencyCode = $this->account?->currency_code ?? 'USD';

        return CurrencyHelper::format($this->fee_amount / 100, $currencyCode);
    }

    public function getHasFeeAttribute(): bool
    {
        return $this->fee_amount > 0 && $this->fee_transaction_id !== null;
    }

    public function getPaymentAmountInMajorUnitsAttribute(): float
    {
        return $this->payment_amount / 100;
    }

    public function getAmountReceivedInMajorUnitsAttribute(): float
    {
        return $this->amount_received / 100;
    }

    public function getFeeAmountInMajorUnitsAttribute(): float
    {
        return $this->fee_amount / 100;
    }

    public function getIsVoidedAttribute(): bool
    {
        return $this->voided_at !== null;
    }
}
