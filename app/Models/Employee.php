<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'email',
        'joining_date',
        'base_salary_pkr',
        'iban',
        'bank_name',
        'status',
        'termination_date',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'termination_date' => 'date',
        ];
    }

    /**
     * Get formatted salary for display
     */
    public function getFormattedSalaryAttribute(): string
    {
        return CurrencyHelper::format($this->base_salary_pkr / 100, 'PKR');
    }

    /**
     * Get salary in major units for form editing
     */
    public function getSalaryInMajorUnitsAttribute(): float
    {
        return $this->base_salary_pkr / 100;
    }

    /**
     * Check if employee is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope to filter active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get all payrolls for this employee
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
