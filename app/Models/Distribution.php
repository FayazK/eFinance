<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'distribution_number',
        'status',
        'period_start',
        'period_end',
        'total_revenue_pkr',
        'total_expenses_pkr',
        'calculated_net_profit_pkr',
        'adjusted_net_profit_pkr',
        'distributed_amount_pkr',
        'processed_at',
        'notes',
        'adjustment_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_revenue_pkr' => 'integer',
            'total_expenses_pkr' => 'integer',
            'calculated_net_profit_pkr' => 'integer',
            'adjusted_net_profit_pkr' => 'integer',
            'distributed_amount_pkr' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'processed_at' => 'date',
        ];
    }

    // === RELATIONSHIPS ===

    public function lines(): HasMany
    {
        return $this->hasMany(DistributionLine::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    // === ACCESSORS ===

    public function getFormattedRevenueAttribute(): string
    {
        return CurrencyHelper::format($this->total_revenue_pkr / 100, 'PKR');
    }

    public function getFormattedExpensesAttribute(): string
    {
        return CurrencyHelper::format($this->total_expenses_pkr / 100, 'PKR');
    }

    public function getFormattedNetProfitAttribute(): string
    {
        $netProfit = $this->adjusted_net_profit_pkr ?? $this->calculated_net_profit_pkr;

        return CurrencyHelper::format($netProfit / 100, 'PKR');
    }

    public function getFinalNetProfitAttribute(): int
    {
        return $this->adjusted_net_profit_pkr ?? $this->calculated_net_profit_pkr;
    }

    public function getIsProcessedAttribute(): bool
    {
        return $this->status === 'processed';
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getIsManuallyAdjustedAttribute(): bool
    {
        return $this->adjusted_net_profit_pkr !== null
            && $this->adjusted_net_profit_pkr !== $this->calculated_net_profit_pkr;
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->format('M j, Y').' - '.$this->period_end->format('M j, Y');
    }

    // === SCOPES ===

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
}
