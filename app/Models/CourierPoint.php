<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'courier_service_id',
        'code',
        'type',
        'name',
        'description',
        'street',
        'building_number',
        'apartment_number',
        'city',
        'postal_code',
        'country_code',
        'latitude',
        'longitude',
        'opening_hours',
        'functions',
        'is_active',
        'metadata',
        'external_id',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'functions' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function courierService(): BelongsTo
    {
        return $this->belongsTo(CourierService::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCourier($query, string|int $courier)
    {
        if (is_numeric($courier)) {
            return $query->where('courier_service_id', (int) $courier);
        }

        return $query->whereHas('courierService', fn ($q) => $q->where('code', $courier));
    }

    public function scopeByTypes($query, array $types)
    {
        return $query->whereIn('type', $types);
    }

    public function scopeWithinBBox($query, float $south, float $west, float $north, float $east)
    {
        return $query->whereBetween('latitude', [$south, $north])
            ->whereBetween('longitude', [$west, $east]);
    }
}
