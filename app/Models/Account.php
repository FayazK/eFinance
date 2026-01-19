<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'currency_code',
        'current_balance',
        'account_number',
        'bank_name',
        'is_active',
    ];

    protected $appends = [
        'formatted_balance',
    ];

    protected function casts(): array
    {
        return [
            'current_balance' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get formatted balance for display (e.g., "$ 100.00")
     */
    public function getFormattedBalanceAttribute(): string
    {
        $amount = $this->current_balance / 100;

        return CurrencyHelper::format($amount, $this->currency_code);
    }

    /**
     * Get balance in major units for editing (convert cents to dollars)
     */
    public function getBalanceInMajorUnitsAttribute(): float
    {
        return $this->current_balance / 100;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
