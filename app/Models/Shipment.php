<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Shipment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'uuid', 'customer_id', 'customer_user_id', 'courier_service_id', 'order_id',
        'tracking_number', 'external_id', 'reference_number', 'status',
        'service_type', 'sender_data', 'recipient_data', 'package_data',
        'cost_data', 'cod_amount', 'insurance_amount', 'additional_services',
        'label_url', 'notes', 'shipped_at', 'delivered_at', 'tracking_events',
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
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });

        static::updating(function ($model) {
            // Check if status changed to 'paid'
            if ($model->isDirty('status') && $model->status === 'paid' && $model->getOriginal('status') !== 'paid') {
                // Call InPost API to create shipment and get tracking number
                dispatch(function () use ($model) {
                    try {
                        app(\App\Services\ShipmentService::class)->processInPostShipment($model);
                    } catch (\Exception $e) {
                        \Log::error('InPost API call failed for shipment '.$model->id, [
                            'error' => $e->getMessage(),
                            'shipment_id' => $model->id,
                        ]);
                    }
                })->afterResponse();
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
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
        return match ($this->status) {
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

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'created']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['created', 'printed']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Szkic',
            'created' => 'Utworzona',
            'paid' => 'Opłacona',
            'printed' => 'Etykieta wydrukowana',
            'sent' => 'Nadana',
            'in_transit' => 'W drodze',
            'delivered' => 'Dostarczona',
            'cancelled' => 'Anulowana',
            'returned' => 'Zwrócona',
            default => 'Nieznany'
        };
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'created' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-blue-100 text-blue-800',
            'printed' => 'bg-indigo-100 text-indigo-800',
            'sent' => 'bg-purple-100 text-purple-800',
            'in_transit' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'returned' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getTotalPriceAttribute(): float
    {
        // Return 0 if cost_data is null
        if (! $this->cost_data) {
            return 0.0;
        }

        // Try different possible keys for total cost
        if (isset($this->cost_data['total'])) {
            return (float) $this->cost_data['total'];
        }
        if (isset($this->cost_data['gross'])) {
            return (float) $this->cost_data['gross'];
        }

        // Fallback to detailed calculation
        $basePrice = $this->cost_data['base_price'] ?? $this->cost_data['price'] ?? 0;
        $codFee = $this->cost_data['cod_fee'] ?? 0;
        $insuranceFee = $this->cost_data['insurance_fee'] ?? 0;
        $additionalFees = $this->cost_data['additional_fees'] ?? 0;

        return (float) ($basePrice + $codFee + $insuranceFee + $additionalFees);
    }

    public function getBasePriceAttribute(): float
    {
        return (float) ($this->cost_data['base_price'] ?? 0);
    }

    public function getCodFeeAttribute(): float
    {
        return (float) ($this->cost_data['cod_fee'] ?? 0);
    }

    public function getInsuranceFeeAttribute(): float
    {
        return (float) ($this->cost_data['insurance_fee'] ?? 0);
    }

    public function getTotalCost(): float
    {
        return $this->cost_data['gross'] ?? $this->total_price ?? 0.0;
    }

    public function statusHistory(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')
            ->where('log_name', 'shipment_status')
            ->latest();
    }

    public function courierApiLogs()
    {
        // Table doesn't exist yet - return empty collection
        return collect([]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'tracking_number', 'recipient_data', 'sender_data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
