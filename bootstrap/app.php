<?php

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $rawProxies = env('TRUSTED_PROXIES', '127.0.0.1,::1');
        $proxies = array_filter(array_map('trim', explode(',', $rawProxies)));
        $middleware->trustProxies(at: !empty($proxies) ? $proxies : ['127.0.0.1', '::1']);
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocaleFromSession::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceFilamentTwoFactor::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
