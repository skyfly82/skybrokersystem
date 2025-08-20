<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid', 'company_name', 'company_short_name', 'nip', 'regon', 'krs',
        'company_address', 'city', 'postal_code', 'country', 'phone', 'email',
        'website', 'status', 'credit_limit', 'current_balance', 'api_key',
        'settings', 'notification_preferences', 'contract_signed_at', 'verified_at'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'settings' => 'array',
        'notification_preferences' => 'array',
        'contract_signed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (!$model->api_key) {
                $model->api_key = 'sk_' . Str::random(48);
            }
            if (!$model->notification_preferences) {
                $model->notification_preferences = [
                    'email' => [
                        'shipment_created' => true,
                        'shipment_delivered' => true,
                        'payment_completed' => true,
                        'low_balance' => true,
                    ],
                    'sms' => [
                        'shipment_delivered' => false,
                        'payment_failed' => true,
                    ]
                ];
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function primaryUser(): ?CustomerUser
    {
        return $this->users()->where('is_primary', true)->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function getTotalShipmentsCount(): int
    {
        return $this->shipments()->count();
    }

    public function getMonthlyShipmentsCount(): int
    {
        return $this->shipments()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function canCreateShipment(): bool
    {
        return $this->isActive() && $this->isVerified();
    }

    public function hasLowBalance(): bool
    {
        return $this->current_balance < 100.00;
    }

    public function addBalance(float $amount, string $description = 'Top-up'): Transaction
    {
        return $this->createTransaction('credit', $amount, $description);
    }

    public function deductBalance(float $amount, string $description = 'Shipment cost'): Transaction
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        
        return $this->createTransaction('debit', $amount, $description);
    }

    private function createTransaction(string $type, float $amount, string $description): Transaction
    {
        $balanceBefore = $this->current_balance;
        $balanceAfter = $type === 'credit' 
            ? $balanceBefore + $amount 
            : $balanceBefore - $amount;

        $this->update(['current_balance' => $balanceAfter]);

        return $this->transactions()->create([
            'uuid' => Str::uuid(),
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
        ]);
    }

    public function getNotificationPreference(string $channel, string $type): bool
    {
        return $this->notification_preferences[$channel][$type] ?? false;
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    public function routeNotificationForSms(): string
    {
        return $this->phone;
    }
}