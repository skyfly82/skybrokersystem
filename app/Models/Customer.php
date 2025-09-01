<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, Notifiable, Auditable;

    protected $fillable = [
        'uuid', 'company_name', 'company_short_name', 'nip', 'regon', 'krs',
        'company_address', 'city', 'postal_code', 'country', 'phone', 'email',
        'website', 'status', 'credit_limit', 'current_balance', 'api_key',
        'cod_return_account', 'settlement_account', 'address',
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

    /**
     * Generate individual account number for mBank collect requirements
     * Format: PL + bank code (11) + branch (1140) + customer_id (8 digits) + check (2)
     */
    public function getIndividualAccountNumberAttribute(): string
    {
        // mBank code: 1140 (branch code for corporate accounts)
        $bankCode = '11401140';
        
        // Customer ID padded to 8 digits
        $customerId = str_pad((string)$this->id, 8, '0', STR_PAD_LEFT);
        
        // Generate account number without check digits
        $accountWithoutCheck = $bankCode . $customerId . '00000000';
        
        // Calculate IBAN check digits for Poland (PL)
        $checkString = $accountWithoutCheck . '2521'; // PL = 2521
        $checkDigits = 98 - bcmod($checkString, '97');
        $checkDigits = str_pad((string)$checkDigits, 2, '0', STR_PAD_LEFT);
        
        // Final account number
        $accountNumber = $bankCode . $customerId . '00000000';
        
        return 'PL' . $checkDigits . $accountNumber;
    }

    /**
     * Get formatted individual account number for display
     */
    public function getFormattedIndividualAccountAttribute(): string
    {
        $account = $this->individual_account_number;
        // Format: PL XX XXXX XXXX XXXX XXXX XXXX XXXX
        return substr($account, 0, 2) . ' ' . 
               substr($account, 2, 2) . ' ' .
               substr($account, 4, 4) . ' ' .
               substr($account, 8, 4) . ' ' .
               substr($account, 12, 4) . ' ' .
               substr($account, 16, 4) . ' ' .
               substr($account, 20, 4) . ' ' .
               substr($account, 24, 4);
    }

    /**
     * Alias for current_balance for backward compatibility
     */
    public function getBalanceAttribute(): float
    {
        return (float) $this->current_balance;
    }

    public function users(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
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

    public function addBalance(float $amount, string $description = 'Top-up', $paymentId = null, $transactionableId = null, string $transactionableType = null): Transaction
    {
        return $this->createTransaction('credit', $amount, $description, $paymentId, $transactionableId, $transactionableType);
    }

    public function deductBalance(float $amount, string $description = 'Shipment cost', $paymentId = null, $transactionableId = null, string $transactionableType = null): Transaction
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        
        return $this->createTransaction('debit', $amount, $description, $paymentId, $transactionableId, $transactionableType);
    }

    private function createTransaction(string $type, float $amount, string $description, $paymentId = null, $transactionableId = null, string $transactionableType = null): Transaction
    {
        $balanceBefore = $this->current_balance;
        $balanceAfter = $type === 'credit' 
            ? $balanceBefore + $amount 
            : $balanceBefore - $amount;

        $this->update(['current_balance' => $balanceAfter]);

        return $this->transactions()->create([
            'uuid' => Str::uuid(),
            'payment_id' => $paymentId,
            'transactionable_id' => $transactionableId,
            'transactionable_type' => $transactionableType,
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