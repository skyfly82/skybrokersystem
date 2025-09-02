<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CmsMedia extends Model
{
    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'alt_text',
        'description',
        'metadata',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->uploaded_by) {
                $model->uploaded_by = auth('system_user')->id();
            }
        });

        static::deleting(function ($model) {
            if (Storage::exists($model->path)) {
                Storage::delete($model->path);
            }
        });
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2).' '.$units[$i];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('mime_type', 'like', $type.'/%');
    }
}
