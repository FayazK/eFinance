<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'client_id',
        'start_date',
        'completion_date',
        'status',
        'budget',
        'actual_cost',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'completion_date' => 'date',
            'budget' => 'decimal:2',
            'actual_cost' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProjectLink::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
