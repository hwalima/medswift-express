<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    /** Get a setting value with optional default. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $s = static::where('key', $key)->first();
            return $s ? $s->value : $default;
        });
    }

    /** Set a setting, clearing the cache. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    /** Bulk-update settings by group, clearing cache. */
    public static function saveGroup(string $group, array $data, array $types = []): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['group' => $group, 'value' => $value, 'type' => $types[$key] ?? 'text']
            );
            Cache::forget("setting:{$key}");
        }
    }
}
