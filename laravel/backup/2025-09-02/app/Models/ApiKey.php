<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'customer_id',
        'name',
        'scopes',
        'status',
        'rate_limit_per_minute',
        'rate_limit_per_day',
        'usage_minute',
        'usage_day',
        'usage_minute_reset_at',
        'usage_day_reset_at',
        'expires_at',
        'last_used_at',
        'meta',
    ];

    protected $casts = [
        'scopes' => 'array',
        'meta' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'usage_minute_reset_at' => 'datetime',
        'usage_day_reset_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];

        return in_array($scope, $scopes, true);
    }

    public function withinLimits(): bool
    {
        $now = now();

        // reset windows if needed
        if (! $this->usage_minute_reset_at || $this->usage_minute_reset_at->lt($now->copy()->subMinute())) {
            $this->usage_minute = 0;
            $this->usage_minute_reset_at = $now;
        }

        if (! $this->usage_day_reset_at || $this->usage_day_reset_at->lt($now->copy()->startOfDay())) {
            $this->usage_day = 0;
            $this->usage_day_reset_at = $now;
        }

        $minuteLimit = $this->rate_limit_per_minute ?? config('map.api.rate_limit_per_minute');
        $dayLimit = $this->rate_limit_per_day ?? config('map.api.rate_limit_per_day');

        return ($this->usage_minute ?? 0) < $minuteLimit && ($this->usage_day ?? 0) < $dayLimit;
    }

    public function registerHit(): void
    {
        $this->last_used_at = now();
        $this->usage_minute = ($this->usage_minute ?? 0) + 1;
        $this->usage_day = ($this->usage_day ?? 0) + 1;
        $this->save();
    }
}
