<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceFilamentTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let auth middleware handle redirection
        if (!$user) {
            return $next($request);
        }

        // Only enforce for users with administrative access
        try {
            $panel = Filament::getCurrentPanel() ?? Filament::getPanel('admin');
            if (!$user->canAccessPanel($panel)) {
                return $next($request);
            }
        } catch (\Throwable) {
            return $next($request);
        }

        // If session already verified for 2FA, allow request
        if ($request->session()->get('filament.mfa_verified') === true) {
            return $next($request);
        }

        // Exempt 2FA challenge, setup, and logout routes
        if ($request->is('admin/two-factor*') || $request->is('admin/logout')) {
            return $next($request);
        }

        // Protect Livewire component update requests
        if ($request->is('livewire/update')) {
            $components = $request->input('components', []);
            $isChallengeComponent = false;

            if (is_array($components)) {
                foreach ($components as $component) {
                    $snapshot = json_decode($component['snapshot'] ?? '{}', true);
                    $componentName = $snapshot['memo']['name'] ?? '';
                    if ($componentName === 'admin.two-factor-challenge') {
                        $isChallengeComponent = true;
                        break;
                    }
                }
            }

            if ($isChallengeComponent) {
                return $next($request);
            }

            abort(403, 'Two-factor authentication required.');
        }

        // Only enforce on admin routes
        if (!$request->is('admin*')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Two-factor authentication required.');
        }

        return redirect()->route('filament.admin.auth.two-factor');
    }
}
