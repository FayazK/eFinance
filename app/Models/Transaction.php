<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'category_id',
        'reference_type',
        'reference_id',
        'type',
        'amount',
        'reporting_amount_pkr',
        'reporting_exchange_rate',
        'description',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'reporting_amount_pkr' => 'integer',
            'reporting_exchange_rate' => 'decimal:4',
            'date' => 'date',
        ];
    }

    /**
     * Get formatted amount for display with currency symbol
     */
    public function getFormattedAmountAttribute(): string
    {
        $amount = $this->amount / 100;
        $currencyCode = $this->account->currency_code ?? '';

        return $currencyCode.' '.number_format($amount, 2);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
