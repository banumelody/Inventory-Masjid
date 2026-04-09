<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\BelongsToMasjid;

class Setting extends Model
{
    use BelongsToMasjid;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
        'masjid_id',
    ];

    /**
     * Get the current masjid_id for cache key scoping
     */
    protected static function currentMasjidId(): ?int
    {
        return app()->bound('current_masjid_id') ? app('current_masjid_id') : null;
    }

    /**
     * Get a setting value by key (tenant-scoped)
     */
    public static function get(string $key, $default = null)
    {
        $masjidId = static::currentMasjidId();
        $cacheKey = $masjidId ? "setting.{$masjidId}.{$key}" : "setting.global.{$key}";

        $setting = Cache::rememberForever($cacheKey, function () use ($key, $masjidId) {
            $query = static::withoutGlobalScopes()->where('key', $key);
            if ($masjidId) {
                $query->where('masjid_id', $masjidId);
            } else {
                $query->whereNull('masjid_id');
            }
            return $query->first();
        });

        return $setting?->value ?? $default;
    }

    /**
     * Set a setting value (tenant-scoped)
     */
    public static function set(string $key, $value): void
    {
        $masjidId = static::currentMasjidId();

        static::withoutGlobalScopes()->updateOrCreate(
            ['key' => $key, 'masjid_id' => $masjidId],
            ['value' => $value]
        );

        $cacheKey = $masjidId ? "setting.{$masjidId}.{$key}" : "setting.global.{$key}";
        Cache::forget($cacheKey);

        $allKey = $masjidId ? "settings.all.{$masjidId}" : 'settings.all.global';
        Cache::forget($allKey);
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray(): array
    {
        $masjidId = static::currentMasjidId();
        $cacheKey = $masjidId ? "settings.all.{$masjidId}" : 'settings.all.global';

        return Cache::rememberForever($cacheKey, function () use ($masjidId) {
            $query = static::withoutGlobalScopes();
            if ($masjidId) {
                $query->where('masjid_id', $masjidId);
            } else {
                $query->whereNull('masjid_id');
            }
            return $query->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group (tenant-scoped)
     */
    public static function getByGroup(string $group)
    {
        $masjidId = static::currentMasjidId();

        $query = static::withoutGlobalScopes()->where('group', $group);
        if ($masjidId) {
            $query->where('masjid_id', $masjidId);
        } else {
            $query->whereNull('masjid_id');
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::withoutGlobalScopes()->all();
        foreach ($settings as $setting) {
            $mid = $setting->masjid_id;
            $cacheKey = $mid ? "setting.{$mid}.{$setting->key}" : "setting.global.{$setting->key}";
            Cache::forget($cacheKey);

            $allKey = $mid ? "settings.all.{$mid}" : 'settings.all.global';
            Cache::forget($allKey);
        }
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
