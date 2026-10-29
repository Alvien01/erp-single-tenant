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

        // If this route exists in menu_settings and is disabled, block it
        $menuSetting = MenuSetting::where('route_name', $routeName)->first();

        if ($menuSetting && !$menuSetting->is_active) {
            abort(403, 'Menu ini telah dinonaktifkan oleh administrator.');
        }

        // Check user-specific menu access permission
        $user = $request->user();
        if ($user && $menuSetting && !$user->canAccessMenu($routeName)) {
            abort(403, 'Akses ditolak: Akun Anda tidak memiliki izin untuk mengakses menu ' . ($menuSetting->label ?? $routeName) . '.');
        }

        return $next($request);
    }
}
