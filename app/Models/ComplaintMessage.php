<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintMessage extends Model
{
    protected $fillable = [
        'complaint_id',
        'sender_type',
        'sender_id',
        'message',
        'is_internal'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(CustomerComplaint::class, 'complaint_id');
    }

    public function sender(): BelongsTo
    {
        if ($this->sender_type === 'customer') {
            return $this->belongsTo(CustomerUser::class, 'sender_id');
        }
        
        return $this->belongsTo(SystemUser::class, 'sender_id');
    }
}
