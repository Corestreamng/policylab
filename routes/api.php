<?php

use App\Http\Controllers\Api\V1\StateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/states', [StateApiController::class, 'index']);
    Route::get('/states/{slug}', [StateApiController::class, 'show']);
    Route::get('/stages', [StateApiController::class, 'stages']);
    Route::get('/kpis', [StateApiController::class, 'kpis']);
    Route::get('/export/csv', [StateApiController::class, 'exportCsv']);
});
