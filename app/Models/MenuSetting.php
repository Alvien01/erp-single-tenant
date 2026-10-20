<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuSetting extends Model
{
    protected $fillable = ['route_name', 'label', 'group', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all active route names (cached for performance).
     */
    public static function getActiveRoutes(): array
    {
        return Cache::remember('menu_active_routes', 60 * 5, function () {
            return static::where('is_active', true)->pluck('route_name')->toArray();
        });
    }

    /**
     * Get all menu labels mapped by route_name (cached for performance).
     */
    public static function getMenuLabels(): array
    {
        return Cache::remember('menu_labels_map', 60 * 5, function () {
            return static::pluck('label', 'route_name')->toArray();
        });
    }

    /**
     * Check if a specific route is active.
     */
    public static function isRouteActive(string $routeName): bool
    {
        return in_array($routeName, static::getActiveRoutes());
    }

    /**
     * Clear the cached active routes and labels.
     */
    public static function clearCache(): void
    {
        Cache::forget('menu_active_routes');
        Cache::forget('menu_labels_map');
    }
}
