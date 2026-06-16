<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuSetting extends Model
{
    protected $fillable = ['route_name', 'label', 'group', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
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
     * Check if a specific route is active.
     */
    public static function isRouteActive(string $routeName): bool
    {
        return in_array($routeName, static::getActiveRoutes());
    }

    /**
     * Clear the cached active routes.
     */
    public static function clearCache(): void
    {
        Cache::forget('menu_active_routes');
    }
}
