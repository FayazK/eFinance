<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DepositCurrency;
use App\Helpers\CurrencyHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'base_salary',
        'deposit_currency',
        'bonus',
        'deductions',
        'net_payable',
        'status',
        'paid_at',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'date',
            'month' => 'integer',
            'year' => 'integer',
            'deposit_currency' => DepositCurrency::class,
        ];
    }

    protected static function booted(): void
    {
        // Auto-calculate net_payable before saving
        static::saving(function (Payroll $payroll) {
            $payroll->net_payable = $payroll->base_salary + $payroll->bonus - $payroll->deductions;
        });
    }

    /**
     * Get the employee that owns the payroll
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the transaction record
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get formatted net payable for display (always in PKR)
     */
    public function getFormattedNetPayableAttribute(): string
    {
        return CurrencyHelper::format($this->net_payable / 100, 'PKR');
    }

    /**
     * Get formatted base salary (always in PKR)
     */
    public function getFormattedBaseSalaryAttribute(): string
    {
        return CurrencyHelper::format($this->base_salary / 100, 'PKR');
    }

    /**
     * Get formatted bonus (always in PKR)
     */
    public function getFormattedBonusAttribute(): string
    {
        return CurrencyHelper::format($this->bonus / 100, 'PKR');
    }

    /**
     * Get formatted deductions (always in PKR)
     */
    public function getFormattedDeductionsAttribute(): string
    {
        return CurrencyHelper::format($this->deductions / 100, 'PKR');
    }

    /**
     * Get amounts in major units for form editing
     */
    public function getBonusInMajorUnitsAttribute(): float
    {
        return $this->bonus / 100;
    }

    public function getDeductionsInMajorUnitsAttribute(): float
    {
        return $this->deductions / 100;
    }

    /**
     * Get period label (e.g., "January 2026")
     */
    public function getPeriodLabelAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    /**
     * Check if payroll is pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payroll is paid
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Scope to filter pending payrolls
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter paid payrolls
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
