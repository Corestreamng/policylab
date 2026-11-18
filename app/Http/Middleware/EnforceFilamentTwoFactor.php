<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceFilamentTwoFactor
{
    /**
     * Permitted public Livewire components that any user (guest or unverified admin) may access.
     */
    protected const ALLOWED_PUBLIC_COMPONENTS = [
        'policy-tracker',
        'document-vault',
        'analytics-dashboard',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Unauthenticated guests are not subject to admin 2FA
        if (!$user) {
            return $next($request);
        }

        // 2. Decide administrative privilege strictly from the authenticated user model
        $isAdmin = $user->hasAnyRole(['Super Admin', 'Data Entry Manager', 'Viewer']);
        if (!$isAdmin) {
            return $next($request);
        }

        // 3. If the administrator has verified 2FA in this session, allow request
        if ($request->session()->get('filament.mfa_verified') === true) {
            return $next($request);
        }

        // 4. Exempt two-factor challenge, setup, and logout routes
        if ($request->is('admin/two-factor*') || $request->is('admin/logout')) {
            return $next($request);
        }

        // 5. Intercept Livewire component updates (strict deny-by-default)
        if ($request->is('livewire/update')) {
            $components = $request->input('components', []);

            if (!is_array($components) || empty($components)) {
                abort(403, 'Two-factor authentication required for administrative components.');
            }

            foreach ($components as $component) {
                $snapshot = json_decode($component['snapshot'] ?? '{}', true);
                $name = $snapshot['memo']['name'] ?? '';

                // Only 2FA challenge or explicit allowlisted public components are permitted
                if ($name === 'admin.two-factor-challenge' || in_array($name, self::ALLOWED_PUBLIC_COMPONENTS, true)) {
                    continue;
                }

                // Strict deny-by-default for any bundled or unapproved administrative component
                abort(403, 'Two-factor authentication required for administrative components.');
            }

            return $next($request);
        }

        // 6. Filament Admin Panel routes: require verified 2FA
        if ($request->is('admin*')) {
            if ($request->expectsJson()) {
                abort(403, 'Two-factor authentication required.');
            }

            return redirect()->route('filament.admin.auth.two-factor');
        }

        return $next($request);
    }
}
