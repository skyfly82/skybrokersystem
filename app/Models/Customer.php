<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid', 'company_name', 'company_short_name', 'nip', 'regon', 'krs',
        'company_address', 'city', 'postal_code', 'country', 'phone', 'email',
        'website', 'status', 'credit_limit', 'current_balance', 'api_key',
        'settings', 'notification_preferences', 'contract_signed_at', 'verified_at',
        'verification_code', 'verification_code_expires_at', 'email_verified',
        'verification_token', 'verification_token_expires_at'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'settings' => 'array',
        'notification_preferences' => 'array',
        'contract_signed_at' => 'datetime',
        'verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'verification_token_expires_at' => 'datetime',
        'email_verified' => 'boolean',
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
                        'account_approved' => true,
                    ],
                    'sms' => [
                        'shipment_delivered' => false,
                        'payment_failed' => true,
                        'low_balance' => false,
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

    public function primaryUser(): HasOne
    {
        return $this->hasOne(CustomerUser::class)->where('is_primary', true);
    }
    
    public function getPrimaryUser(): ?CustomerUser
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

    public function updateNotificationPreference(string $channel, string $type, bool $enabled): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$channel][$type] = $enabled;
        $this->update(['notification_preferences' => $preferences]);
    }

    public function canReceiveNotification(string $channel, string $type): bool
    {
        return $this->isActive() && $this->getNotificationPreference($channel, $type);
    }

    public function generateVerificationCode(): array
    {
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $token = \Str::random(64);
        
        // Get expiry times from settings
        $codeExpiry = \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60);
        $linkExpiry = \App\Models\SystemSetting::get('verification_link_expiry_hours', 24);
        
        $this->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes($codeExpiry),
            'verification_token' => $token,
            'verification_token_expires_at' => now()->addHours($linkExpiry),
            'email_verified' => false
        ]);

        return [
            'code' => $code,
            'token' => $token,
            'code_expires_at' => $this->verification_code_expires_at,
            'link_expires_at' => $this->verification_token_expires_at
        ];
    }

    public function verifyCode(string $code): bool
    {
        if ($this->verification_code !== $code) {
            return false;
        }

        if ($this->verification_code_expires_at && $this->verification_code_expires_at->isPast()) {
            return false;
        }

        $this->activateAccount();
        return true;
    }

    public function verifyToken(string $token): bool
    {
        if ($this->verification_token !== $token) {
            return false;
        }

        if ($this->verification_token_expires_at && $this->verification_token_expires_at->isPast()) {
            return false;
        }

        return true; // Token valid, can show verification page
    }

    public function activateAccount(): void
    {
        $this->update([
            'status' => 'active',
            'email_verified' => true,
            'verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'verification_token' => null,
            'verification_token_expires_at' => null
        ]);
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified;
    }

    public function hasValidVerificationCode(): bool
    {
        return $this->verification_code 
            && $this->verification_code_expires_at 
            && $this->verification_code_expires_at->isFuture();
    }

    public function hasValidVerificationToken(): bool
    {
        return $this->verification_token 
            && $this->verification_token_expires_at 
            && $this->verification_token_expires_at->isFuture();
    }

    public function canResendCode(): bool
    {
        // Can resend if no valid code or if last code was sent more than 5 minutes ago
        if (!$this->hasValidVerificationCode()) {
            return true;
        }

        return $this->verification_code_expires_at && 
               $this->verification_code_expires_at->subMinutes(55)->isPast(); // Allow resend 5 mins after sending
    }
}