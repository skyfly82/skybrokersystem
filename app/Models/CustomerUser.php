<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class CustomerUser extends Authenticatable
{
    use Auditable, HasFactory, Notifiable;

    protected $fillable = [
        'uuid', 'customer_id', 'first_name', 'last_name', 'email', 'password',
        'phone', 'role', 'permissions', 'is_active', 'is_primary',
        'notification_preferences', 'last_login_at', 'last_login_ip',
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
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
            if (! $model->notification_preferences) {
                $model->notification_preferences = [
                    'email' => [
                        'shipment_status_update' => true,
                        'payment_receipt' => true,
                        'system_updates' => false,
                    ],
                    'sms' => [
                        'shipment_delivered' => false,
                        'urgent_notifications' => true,
                    ],
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
        return $this->first_name.' '.$this->last_name;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_primary;
    }

    public function canCreateShipments(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'user', 'warehouse']);
    }

    public function canViewReports(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'accountant']);
    }

    public function canManagePayments(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'accountant']);
    }

    public function canAccessFinancials(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'accountant']);
    }

    public function canManageShipments(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'user', 'warehouse']);
    }

    public function canCreateUsers(): bool
    {
        return $this->is_active && ($this->role === 'admin' || $this->is_primary);
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isWarehouse(): bool
    {
        return $this->role === 'warehouse';
    }

    public function canTransferAdminRights(): bool
    {
        return $this->is_active && $this->is_primary;
    }

    public function transferAdminRightsTo(CustomerUser $newAdmin): bool
    {
        if (! $this->canTransferAdminRights()) {
            return false;
        }

        if ($newAdmin->customer_id !== $this->customer_id) {
            return false;
        }

        // Transfer primary status
        $this->is_primary = false;
        $this->role = 'user'; // Demote current primary to regular user
        $this->save();

        // Promote new user to primary admin
        $newAdmin->is_primary = true;
        $newAdmin->role = 'admin';
        $newAdmin->is_active = true;
        $newAdmin->save();

        return true;
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
