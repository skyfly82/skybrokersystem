<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CourierApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'courier_service_id',
        'loggable_type',
        'loggable_id',
        'action',
        'method',
        'endpoint',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'response_time_ms',
        'error_message',
        'context',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
        'response_body' => 'array',
        'response_time_ms' => 'integer',
        'context' => 'array',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function courierService(): BelongsTo
    {
        return $this->belongsTo(CourierService::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isSuccessful(): bool
    {
        return $this->response_status >= 200 && $this->response_status < 300;
    }

    public function getStatusColorClassAttribute(): string
    {
        if ($this->response_status >= 200 && $this->response_status < 300) {
            return 'bg-green-100 text-green-800';
        } elseif ($this->response_status >= 400 && $this->response_status < 500) {
            return 'bg-yellow-100 text-yellow-800';
        } elseif ($this->response_status >= 500) {
            return 'bg-red-100 text-red-800';
        }

        return 'bg-gray-100 text-gray-800';
    }

    public function getFormattedRequestBodyAttribute(): string
    {
        if (is_array($this->request_body)) {
            return json_encode($this->request_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $this->request_body ?? '';
    }

    public function getFormattedResponseBodyAttribute(): string
    {
        if (is_array($this->response_body)) {
            return json_encode($this->response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $this->response_body ?? '';
    }
}
