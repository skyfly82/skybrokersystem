<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CustomerComplaint extends Model
{
    protected $fillable = [
        'complaint_number',
        'customer_id',
        'customer_user_id',
        'shipment_id',
        'complaint_topic_id',
        'subject',
        'description',
        'priority',
        'status',
        'freshdesk_ticket_id',
        'freshdesk_data',
        'assigned_to',
        'assigned_at',
        'resolution',
        'resolved_at',
        'resolved_by',
        'contact_email',
        'contact_phone',
        'preferred_contact_method'
    ];

    protected $casts = [
        'freshdesk_data' => 'array',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->complaint_number)) {
                $model->complaint_number = static::generateComplaintNumber();
            }
        });
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ComplaintTopic::class, 'complaint_topic_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ComplaintMessage::class, 'complaint_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ComplaintFile::class, 'complaint_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeModifiedBy(CustomerUser $user): bool
    {
        return $this->customer_user_id === $user->id && !$this->isResolved();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-red-100 text-red-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'waiting_customer' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public static function generateComplaintNumber(): string
    {
        $year = date('Y');
        $lastComplaint = static::whereYear('created_at', $year)
                              ->orderBy('id', 'desc')
                              ->first();
        
        $sequence = $lastComplaint ? 
                   intval(substr($lastComplaint->complaint_number, -6)) + 1 : 1;
        
        return sprintf('COMP-%s-%06d', $year, $sequence);
    }

    public function markAsResolved(string $resolution, int $resolvedBy): bool
    {
        return $this->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy
        ]);
    }

    public function assignTo(int $userId): bool
    {
        return $this->update([
            'assigned_to' => $userId,
            'assigned_at' => now(),
            'status' => 'in_progress'
        ]);
    }
}
