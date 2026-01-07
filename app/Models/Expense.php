<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'account_id',
        'category_id',
        'transaction_id',
        'amount',
        'currency_code',
        'vendor',
        'description',
        'expense_date',
        'recurrence_frequency',
        'recurrence_interval',
        'recurrence_start_date',
        'recurrence_end_date',
        'next_occurrence_date',
        'last_processed_date',
        'is_recurring',
        'is_active',
        'exchange_rate',
        'reporting_amount_pkr',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'reporting_amount_pkr' => 'integer',
            'exchange_rate' => 'decimal:4',
            'expense_date' => 'date',
            'recurrence_start_date' => 'date',
            'recurrence_end_date' => 'date',
            'next_occurrence_date' => 'date',
            'last_processed_date' => 'date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipts')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
            ->maxFileSize(5 * 1024 * 1024); // 5MB
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute(): string
    {
        return CurrencyHelper::format($this->amount / 100, $this->currency_code);
    }

    public function getFormattedReportingAmountAttribute(): ?string
    {
        return $this->reporting_amount_pkr
            ? CurrencyHelper::format($this->reporting_amount_pkr / 100, 'PKR')
            : null;
    }

    public function getAmountInMajorUnitsAttribute(): float
    {
        return $this->amount / 100;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getIsProcessedAttribute(): bool
    {
        return $this->status === 'processed';
    }

    public function getIsRecurringTemplateAttribute(): bool
    {
        return $this->is_recurring && $this->is_active;
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeRecurringTemplates($query)
    {
        return $query->where('is_recurring', true)
            ->where('is_active', true);
    }

    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
}
