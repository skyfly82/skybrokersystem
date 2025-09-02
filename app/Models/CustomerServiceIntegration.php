<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceIntegration extends Model
{
    protected $fillable = [
        'service_name',
        'service_type',
        'api_key',
        'api_url',
        'webhook_secret',
        'configuration',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key',
        'webhook_secret',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByService($query, string $service)
    {
        return $query->where('service_name', $service);
    }

    public function isConnected(): bool
    {
        return ! empty($this->api_key) && ! empty($this->api_url);
    }

    public function canReceiveWebhooks(): bool
    {
        return $this->isConnected() && ! empty($this->webhook_secret);
    }
}
