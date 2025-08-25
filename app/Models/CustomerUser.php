<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomerUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid', 'customer_id', 'first_name', 'last_name', 'email', 'password',
        'phone', 'role', 'permissions', 'is_active', 'is_primary',
        'notification_preferences', 'last_login_at', 'last_login_ip'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'notification_preferences' => 'array',
        'last_login_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (!$model->notification_preferences) {
                $model->notification_preferences = [
                    'email' => [
                        'shipment_status_update' => true,
                        'payment_receipt' => true,
                        'system_updates' => false,
                    ],
                    'sms' => [
                        'shipment_delivered' => false,
                        'urgent_notifications' => true,
                    ]
                ];
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_primary;
    }

    public function canCreateShipments(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'user']);
    }

    public function canViewReports(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'user']);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_primary) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    public function routeNotificationForSms(): string
    {
        return $this->phone;
    }

    public function getNotificationPreference(string $channel, string $type): bool
    {
        return $this->notification_preferences[$channel][$type] ?? false;
    }

    public function updateNotificationPreference(string $channel, string $type, bool $enabled): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$channel][$type] = $enabled;
        $this->update(['notification_preferences' => $preferences]);
    }
}