<?php

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
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy');

// Secure Signed Document Routes
Route::get('/documents/{document:uuid}/download', [DocumentController::class, 'download'])
    ->name('documents.download');

Route::get('/documents/{document:uuid}/stream', [DocumentController::class, 'stream'])
    ->name('documents.stream');

// SEO Routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
