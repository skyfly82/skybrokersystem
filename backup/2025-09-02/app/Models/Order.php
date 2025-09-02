<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'customer_id', 'customer_user_id', 'order_number',
        'status', 'total_amount', 'currency', 'shipping_data', 
        'notes', 'paid_at', 'completed_at'
    ];

    protected $casts = [
        'shipping_data' => 'array',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = Str::uuid();
            }
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Helper methods
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastOrder ? intval(substr($lastOrder->order_number, -6)) + 1 : 1;
        
        return 'ORDER-' . $year . '-' . str_pad((string)$nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // Status methods
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Oczekujące',
            'paid' => 'Opłacone', 
            'processing' => 'W realizacji',
            'completed' => 'Zakończone',
            'cancelled' => 'Anulowane',
            default => 'Nieznany',
        };
    }

    public function getStatusColorClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Business logic
    public function canBePaid(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // Update all shipments to created status
        $this->shipments()->update(['status' => 'created']);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        
        // Update shipments back to draft
        $this->shipments()->update(['status' => 'draft']);
    }
}