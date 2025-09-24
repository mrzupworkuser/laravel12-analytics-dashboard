<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SegmentsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:viewAnalytics')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'fetchData'])->name('analytics.data');
    Route::get('/analytics/export', [AnalyticsController::class, 'exportCsv'])->name('analytics.export');

    // Additional module routes
    Route::get('/analytics/reports/summary', [ReportsController::class, 'summary'])->name('analytics.reports.summary');
    Route::get('/analytics/reports/top-customers', [ReportsController::class, 'topCustomers'])->name('analytics.reports.top_customers');
    Route::get('/analytics/segments/status-breakdown', [SegmentsController::class, 'statusBreakdown'])->name('analytics.segments.status_breakdown');
    Route::get('/analytics/settings', [SettingsController::class, 'index'])->name('analytics.settings');
    Route::post('/analytics/settings', [SettingsController::class, 'save'])->name('analytics.settings.save');
});


