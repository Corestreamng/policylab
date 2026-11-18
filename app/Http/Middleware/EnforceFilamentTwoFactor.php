<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceFilamentTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let Filament's auth middleware handle redirection to login
        if (!$user) {
            return $next($request);
        }

        // Only enforce on admin routes
        if (!$request->is('admin*')) {
            return $next($request);
        }

        // Exempt two-factor challenge, setup, and logout routes
        if ($request->is('admin/two-factor*') || $request->is('admin/logout')) {
            return $next($request);
        }

        // Verify if session has active 2FA approval
        if ($request->session()->get('filament.mfa_verified') === true) {
            return $next($request);
        }

        // Redirect unverified admin to two-factor verification
        return redirect()->route('filament.admin.auth.two-factor');
    }
}
