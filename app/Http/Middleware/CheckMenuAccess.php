<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MenuSetting;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     * Block access to routes that have been disabled in Manage Menu.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        // Always allow these system routes
        $alwaysAllowed = [
            'home', 'login', 'register', 'logout', 'profile',
            'password.request', 'password.reset', 'password.email',
            'verification.notice', 'verification.verify', 'verification.send',
            'midtrans.webhook',
            'sales-quotations.pdf', 'sales-quotations.pdf.stream',
        ];

        if (in_array($routeName, $alwaysAllowed)) {
            return $next($request);
        }

        // Sub-pages (create/edit/show/pdf/...) inherit the permission of their parent menu
        // e.g. "sales.create" -> "sales", "sales-quotations.pdf.stream" -> "sales-quotations"
        $menuRoute = self::resolveMenuRoute($routeName);

        // If this route exists in menu_settings and is disabled, block it
        $menuSetting = $menuRoute ? MenuSetting::where('route_name', $menuRoute)->first() : null;

        if ($menuSetting && !$menuSetting->is_active) {
            abort(403, 'Menu ini telah dinonaktifkan oleh administrator.');
        }

        // Check user-specific menu access permission
        $user = $request->user();
        if ($user && $menuSetting && !$user->canAccessMenu($menuRoute)) {
            abort(403, 'Akses ditolak: Akun Anda tidak memiliki izin untuk mengakses menu ' . ($menuSetting->label ?? $menuRoute) . '.');
        }

        return $next($request);
    }

    /**
     * Sub-action segments that should inherit their parent menu's access rules.
     */
    public const SUB_ACTIONS = ['create', 'edit', 'show', 'view', 'detail', 'pdf', 'stream', 'print', 'download', 'export'];

    /**
     * Strip trailing sub-action segments from a route name to get the parent menu route.
     */
    public static function resolveMenuRoute(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        $segments = explode('.', $routeName);
        while (count($segments) > 1 && in_array(end($segments), self::SUB_ACTIONS, true)) {
            array_pop($segments);
        }

        return implode('.', $segments);
    }
}
