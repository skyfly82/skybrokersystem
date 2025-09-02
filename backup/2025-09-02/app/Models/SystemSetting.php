<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'group', 'description'
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("system_setting_{$key}", 3600, function() use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return match($setting->type) {
                'integer' => (int) $setting->value,
                'boolean' => (bool) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value
            };
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general', ?string $description = null): void
    {
        $valueToStore = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueToStore,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );

        Cache::forget("system_setting_{$key}");
    }

    public static function getByGroup(string $group): array
    {
        return Cache::remember("system_settings_group_{$group}", 3600, function() use ($group) {
            return static::where('group', $group)
                ->get()
                ->mapWithKeys(function($setting) {
                    $value = match($setting->type) {
                        'integer' => (int) $setting->value,
                        'boolean' => (bool) $setting->value,
                        'json' => json_decode($setting->value, true),
                        default => $setting->value
                    };
                    return [$setting->key => $value];
                })
                ->toArray();
        });
    }
}
