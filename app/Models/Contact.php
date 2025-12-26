<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Nnjeim\World\Models\Country;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'client_id',
        'address',
        'city',
        'state',
        'country_id',
        'primary_phone',
        'primary_email',
        'additional_phones',
        'additional_emails',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'additional_phones' => 'array',
            'additional_emails' => 'array',
        ];
    }

    /**
     * Get the client that the contact belongs to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the country that the contact belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the full name attribute.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }
}
