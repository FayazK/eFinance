<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'email',
        'tax_id',
        'vat_number',
    ];

    /**
     * Get the invoices for the company.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->logo)) {
            return null;
        }

        return Storage::url($this->logo);
    }

    /**
     * Get the logo file path for PDF generation.
     */
    public function getLogoPathAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->logo)) {
            return null;
        }

        return Storage::disk('public')->path($this->logo);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }
}
