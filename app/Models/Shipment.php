<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'customer_id', 'customer_user_id', 'courier_service_id',
        'tracking_number', 'external_id', 'reference_number', 'status',
        'service_type', 'sender_data', 'recipient_data', 'package_data',
        'cost_data', 'cod_amount', 'insurance_amount', 'additional_services',
        'label_url', 'notes', 'shipped_at', 'delivered_at', 'tracking_events'
    ];

    protected $casts = [
        'sender_data' => 'array',
        'recipient_data' => 'array',
        'package_data' => 'array',
        'cost_data' => 'array',
        'additional_services' => 'array',
        'tracking_events' => 'array',
        'cod_amount' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    public function courierService(): BelongsTo
    {
        return $this->belongsTo(CourierService::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'created' => 'blue',
            'printed' => 'indigo',
            'dispatched' => 'yellow',
            'in_transit' => 'purple',
            'out_for_delivery' => 'orange',
            'delivered' => 'green',
            'returned' => 'red',
            'cancelled' => 'red',
            'failed' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Szkic',
            'created' => 'Utworzona',
            'printed' => 'Wydrukowana',
            'dispatched' => 'Nadana',
            'in_transit' => 'W transporcie',
            'out_for_delivery' => 'W doręczeniu',
            'delivered' => 'Dostarczona',
            'returned' => 'Zwrócona',
            'cancelled' => 'Anulowana',
            'failed' => 'Błąd',
            default => 'Nieznany',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'created']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['created', 'printed']);
    }

    public function getTotalCost(): float
    {
        return $this->cost_data['gross'] ?? 0.0;
    }
}