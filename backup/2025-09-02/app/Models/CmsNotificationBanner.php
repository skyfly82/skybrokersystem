<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CmsNotificationBanner extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'position',
        'is_active',
        'start_date',
        'end_date',
        'priority',
        'display_rules',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'display_rules' => 'array'
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->created_by) {
                $model->created_by = auth('system_user')->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        return true;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'success' => 'bg-green-50 text-green-800 border-green-200',
            'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'error' => 'bg-red-50 text-red-800 border-red-200',
            default => 'bg-blue-50 text-blue-800 border-blue-200'
        };
    }

    public function shouldDisplayOnPage(string $currentPage): bool
    {
        if (!$this->display_rules) {
            return true;
        }
        
        $rules = $this->display_rules;
        
        if (isset($rules['pages'])) {
            return in_array($currentPage, $rules['pages']);
        }
        
        if (isset($rules['exclude_pages'])) {
            return !in_array($currentPage, $rules['exclude_pages']);
        }
        
        return true;
    }
}
