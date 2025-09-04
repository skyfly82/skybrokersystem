<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class CmsSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'updated_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_by = auth('system_user')->id();
        });

        static::saved(function ($model) {
            Cache::forget('cms_setting_'.$model->key);
            Cache::forget('cms_settings_group_'.$model->group);
        });

        static::deleted(function ($model) {
            Cache::forget('cms_setting_'.$model->key);
            Cache::forget('cms_settings_group_'.$model->group);
        });
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'updated_by');
    }

    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
            default => $value
        };
    }

    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value
        };
    }

    public static function get(string $key, $default = null)
    {
        return Cache::remember(
            'cms_setting_'.$key,
            now()->addHours(24),
            function () use ($key, $default) {
                $setting = static::where('key', $key)->first();

                return $setting ? $setting->value : $default;
            }
        );
    }

    public static function set(string $key, $value, string $type = 'text', string $group = 'general', ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    public static function getGroup(string $group): array
    {
        return Cache::remember(
            'cms_settings_group_'.$group,
            now()->addHours(24),
            function () use ($group) {
                return static::where('group', $group)
                    ->pluck('value', 'key')
                    ->toArray();
            }
        );
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
