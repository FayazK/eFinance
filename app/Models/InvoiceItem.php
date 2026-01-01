<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'amount' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return CurrencyHelper::format(
            $this->unit_price / 100,
            $this->invoice->currency_code
        );
    }

    public function getFormattedAmountAttribute(): string
    {
        return CurrencyHelper::format(
            $this->amount / 100,
            $this->invoice->currency_code
        );
    }

    public function getUnitPriceInMajorUnitsAttribute(): float
    {
        return $this->unit_price / 100;
    }

    public function getAmountInMajorUnitsAttribute(): float
    {
        return $this->amount / 100;
    }
}
