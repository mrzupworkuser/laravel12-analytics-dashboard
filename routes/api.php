<?php

/**
 * API routes for analytics data endpoints.
 *
 * All routes are protected by 'viewAnalytics' gate authorization.
 *
 * @author Manohar Zarkar
 */

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Analytics API endpoints with authorization
Route::middleware('can:viewAnalytics')->group(function () {
    // Get analytics data (metrics and time-series)
    Route::get('/analytics', [AnalyticsController::class, 'fetchData']);
});


