<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'shareholder_id',
        'equity_percentage_snapshot',
        'allocated_amount_pkr',
        'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'equity_percentage_snapshot' => 'decimal:2',
            'allocated_amount_pkr' => 'integer',
        ];
    }

    // === RELATIONSHIPS ===

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(Shareholder::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // === ACCESSORS ===

    public function getFormattedAllocatedAmountAttribute(): string
    {
        return CurrencyHelper::format($this->allocated_amount_pkr / 100, 'PKR');
    }

    public function getFormattedEquityAttribute(): string
    {
        return number_format((float) $this->equity_percentage_snapshot, 2).'%';
    }
}
