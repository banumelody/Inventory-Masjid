<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return static::where('key', $key)->first();
        });

        return $setting?->value ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting.{$key}");
        Cache::forget('settings.all');
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
        Cache::forget('settings.all');
    }

    /**
     * Get app name with fallback
     */
    public static function appName(): string
    {
        return static::get('app_name', 'Inventory Masjid');
    }

    /**
     * Get organization name
     */
    public static function orgName(): string
    {
        return static::get('org_name', '');
    }

    /**
     * Check if logo exists
     */
    public static function hasLogo(): bool
    {
        $logo = static::get('app_logo');
        return !empty($logo);
    }

    /**
     * Get logo URL
     */
    public static function logoUrl(): ?string
    {
        $logo = static::get('app_logo');
        if (empty($logo)) {
            return null;
        }
        return asset('storage/' . $logo);
    }
}
