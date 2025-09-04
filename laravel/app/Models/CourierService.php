<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierService extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'slug', 'logo_url', 'description', 'api_endpoint',
        'api_credentials', 'is_active', 'is_sandbox', 'supported_services',
        'service_configuration', 'pricing_rules', 'sort_order',
    ];

    protected $casts = [
        'api_credentials' => 'encrypted:array',
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'supported_services' => 'array',
        'service_configuration' => 'array',
        'pricing_rules' => 'array',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isSandbox(): bool
    {
        return $this->is_sandbox;
    }

    public function supportsService(string $serviceType): bool
    {
        return isset($this->supported_services[$serviceType]);
    }

    public function getServiceName(string $serviceType): ?string
    {
        return $this->supported_services[$serviceType] ?? null;
    }
}
