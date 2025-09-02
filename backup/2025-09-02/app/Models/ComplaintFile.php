<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintFile extends Model
{
    protected $fillable = [
        'complaint_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'uploaded_by'
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(CustomerComplaint::class, 'complaint_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'uploaded_by');
    }
}
