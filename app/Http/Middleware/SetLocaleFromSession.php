<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->session()->has('app_locale')) {
            $locale = $request->session()->get('app_locale');
            if (in_array($locale, ['en', 'fr', 'ha', 'yo', 'ig', 'ar'], true)) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
