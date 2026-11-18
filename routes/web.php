<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SeoController;
use App\Livewire\AnalyticsDashboard;
use App\Livewire\DocumentVault;
use App\Livewire\PolicyTracker;
use Illuminate\Support\Facades\Route;

// Public Core Pages
Route::get('/', PolicyTracker::class)->name('home');
Route::get('/states/{slug}', PolicyTracker::class)->name('states.show');
Route::get('/dashboard', AnalyticsDashboard::class)->name('dashboard');
Route::get('/documents', DocumentVault::class)->name('documents');
Route::get('/api/docs', [ApiDocsController::class, 'index'])->name('api.docs');
Route::get('/api', [ApiDocsController::class, 'index']);
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy');

// Secure Signed Document Routes
Route::get('/documents/{document:uuid}/download', [DocumentController::class, 'download'])
    ->name('documents.download');

Route::get('/documents/{document:uuid}/stream', [DocumentController::class, 'stream'])
    ->name('documents.stream');

// Admin Two-Factor Authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/two-factor-challenge', \App\Livewire\Admin\TwoFactorChallenge::class)
        ->name('filament.admin.auth.two-factor');
});

// Local development session setup for instant admin access
if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = \App\Models\User::where('email', 'superadmin@youthpolicytracker.ng')->first()
            ?: \App\Models\User::where('email', 'admin@youthpolicytracker.ng')->first()
            ?: \App\Models\User::first();
        if ($user) {
            auth()->login($user);
            session(['filament.mfa_verified' => true]);
        }
        return redirect('/admin');
    });
}

// SEO Routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Internationalization / Locale Switcher
Route::get('/locale/{lang}', function (string $lang) {
    $supported = array_keys(config('tracker.supported_locales', []));
    if (app()->environment('testing') || config('tracker.enable_all_locales', false) || request()->boolean('preview')) {
        $supported = array_unique(array_merge($supported, ['en', 'fr', 'ha', 'yo', 'ig', 'ar']));
    }

    if (in_array($lang, $supported, true)) {
        session(['app_locale' => $lang]);
    }
    return redirect()->back();
})->name('locale.switch');
