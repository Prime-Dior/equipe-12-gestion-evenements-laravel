<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'date',
        'location',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'capacity' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now());
    }

    public function getPlacesRemainingAttribute(): int
    {
        if (array_key_exists('registrations_count', $this->attributes)) {
            return max(0, $this->capacity - (int) $this->attributes['registrations_count']);
        }

        $registered = Schema::hasTable('registrations') ? $this->registrations()->count() : 0;

        return max(0, $this->capacity - $registered);
    }

    public function isPast(): bool
    {
        return $this->date->isPast();
    }

    public function isFull(): bool
    {
        return $this->places_remaining === 0;
    }
}
