<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RegistrationStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_type',
        'registration_method',
        'source',
        'campaign', 
        'medium',
        'user_agent',
        'ip_address',
        'country',
        'city',
        'started_at',
        'completed_at',
        'successful',
        'form_steps',
        'errors',
        'customer_id',
        'customer_user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'successful' => 'boolean',
        'form_steps' => 'array',
        'errors' => 'array',
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

    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('registration_method', $method);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeLastDays($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public static function trackStart(string $customerType, string $method, array $utmData = [], ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return static::create([
            'customer_type' => $customerType,
            'registration_method' => $method,
            'source' => $utmData['source'] ?? null,
            'campaign' => $utmData['campaign'] ?? null,
            'medium' => $utmData['medium'] ?? null,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'started_at' => now(),
            'successful' => false,
        ]);
    }

    public function markCompleted(Customer $customer, CustomerUser $user): void
    {
        $this->update([
            'completed_at' => now(),
            'successful' => true,
            'customer_id' => $customer->id,
            'customer_user_id' => $user->id,
        ]);
    }

    public function markFailed(array $errors = []): void
    {
        $this->update([
            'completed_at' => now(),
            'successful' => false,
            'errors' => $errors,
        ]);
    }

    public function addFormStep(string $step): void
    {
        $steps = $this->form_steps ?? [];
        $steps[] = [
            'step' => $step,
            'timestamp' => now()->toISOString(),
        ];
        
        $this->update(['form_steps' => $steps]);
    }

    public function getDurationInSeconds(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    public function isFromSocialMedia(): bool
    {
        return in_array($this->registration_method, ['google', 'facebook', 'linkedin']);
    }

    public function isFromUtmCampaign(): bool
    {
        return !empty($this->source) || !empty($this->campaign);
    }
}
