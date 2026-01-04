<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shareholder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'equity_percentage',
        'is_office_reserve',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'equity_percentage' => 'decimal:2',
            'is_office_reserve' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // === RELATIONSHIPS ===

    public function distributionLines(): HasMany
    {
        return $this->hasMany(DistributionLine::class);
    }

    // === ACCESSORS ===

    public function getFormattedEquityAttribute(): string
    {
        return number_format((float) $this->equity_percentage, 2).'%';
    }

    public function getIsHumanPartnerAttribute(): bool
    {
        return ! $this->is_office_reserve;
    }

    // === SCOPES ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHumanPartners($query)
    {
        return $query->where('is_office_reserve', false);
    }
}
