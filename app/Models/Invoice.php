<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'status',
        'client_id',
        'project_id',
        'currency_code',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'issue_date',
        'due_date',
        'paid_at',
        'sent_at',
        'voided_at',
        'notes',
        'terms',
        'client_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'tax_amount' => 'integer',
            'total_amount' => 'integer',
            'amount_paid' => 'integer',
            'balance_due' => 'integer',
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'date',
            'sent_at' => 'date',
            'voided_at' => 'date',
        ];
    }

    // === RELATIONSHIPS ===

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    // === ACCESSORS ===

    public function getFormattedSubtotalAttribute(): string
    {
        return CurrencyHelper::format($this->subtotal / 100, $this->currency_code);
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return CurrencyHelper::format($this->tax_amount / 100, $this->currency_code);
    }

    public function getFormattedTotalAttribute(): string
    {
        return CurrencyHelper::format($this->total_amount / 100, $this->currency_code);
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return CurrencyHelper::format($this->amount_paid / 100, $this->currency_code);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return CurrencyHelper::format($this->balance_due / 100, $this->currency_code);
    }

    public function getSubtotalInMajorUnitsAttribute(): float
    {
        return $this->subtotal / 100;
    }

    public function getTaxAmountInMajorUnitsAttribute(): float
    {
        return $this->tax_amount / 100;
    }

    public function getTotalInMajorUnitsAttribute(): float
    {
        return $this->total_amount / 100;
    }

    public function getAmountPaidInMajorUnitsAttribute(): float
    {
        return $this->amount_paid / 100;
    }

    public function getBalanceInMajorUnitsAttribute(): float
    {
        return $this->balance_due / 100;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid'
            && $this->status !== 'void'
            && $this->due_date->isPast();
    }

    public function getIsPayableAttribute(): bool
    {
        return in_array($this->status, ['sent', 'partial', 'overdue'])
            && $this->balance_due > 0;
    }
}
