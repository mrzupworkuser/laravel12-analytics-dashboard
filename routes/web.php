<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:viewAnalytics')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'fetchData'])->name('analytics.data');
    Route::get('/analytics/export', [AnalyticsController::class, 'exportCsv'])->name('analytics.export');
});


