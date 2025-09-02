<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class SystemUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'role', 'is_active',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageUsers(): bool
    {
        return $this->role === 'super_admin';
    }

    public function canCreateEmployees(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageCustomers(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin();
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isMarketing(): bool
    {
        return $this->role === 'marketing';
    }

    public function canManageContent(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'marketing']);
    }
}
