<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'plan',
        'status',
        'request_quota_monthly',
        'requests_used_this_period',
        'period_started_at',
        'period_ends_at',
        'auto_renew',
        'metadata',
    ];

    protected $casts = [
        'auto_renew' => 'boolean',
        'metadata' => 'array',
        'period_started_at' => 'datetime',
        'period_ends_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->period_ends_at || $this->period_ends_at->isFuture());
    }

    public function canConsume(int $n = 1): bool
    {
        if (!$this->isActive()) return false;
        $quota = $this->request_quota_monthly ?? 0;
        return ($this->requests_used_this_period ?? 0) + $n <= $quota;
    }

    public function consume(int $n = 1): void
    {
        $this->requests_used_this_period = ($this->requests_used_this_period ?? 0) + $n;
        $this->save();
    }
}

