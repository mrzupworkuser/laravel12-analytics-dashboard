<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:viewAnalytics')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'fetchData']);
});


