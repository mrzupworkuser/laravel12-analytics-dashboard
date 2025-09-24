<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnalyticsDataRequest;
use App\Http\Resources\AnalyticsResource;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Analytics pages and lightweight data endpoints.
 *
 * @author Manohar Zarkar
 */
class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }

    /**
     * Show the dashboard with headline metrics and initial charts.
     *
     * @return View
     */
    public function index(): View
    {
        $metrics = $this->analyticsService->getHeadlineMetrics();
        $series = $this->analyticsService->getTimeSeriesData(14);

        return view('analytics.index', [
            'metrics' => $metrics,
            'series' => $series,
        ]);
    }

    /**
     * JSON endpoint for refreshing metrics and time-series.
     *
     * @return JsonResponse
     */
    public function fetchData(AnalyticsDataRequest $request): JsonResponse
    {
        $days = (int) ($request->validated()['days'] ?? 14);

        $ttl = (int) config('analytics.cache_ttl_seconds', 30);
        $cacheKey = "analytics:data:days:" . $days;

        $payload = Cache::remember($cacheKey, $ttl, function () use ($days) {
            return [
                'metrics' => $this->analyticsService->getHeadlineMetrics(),
                'series' => $this->analyticsService->getTimeSeriesData($days),
            ];
        });

        return (new AnalyticsResource($payload))->response();
    }

    /**
     * CSV export of time-series data for the selected window.
     *
     * @param  AnalyticsDataRequest  $request
     * @return StreamedResponse
     */
    public function exportCsv(AnalyticsDataRequest $request): StreamedResponse
    {
        $days = (int) ($request->validated()['days'] ?? 14);

        $series = $this->analyticsService->getTimeSeriesData($days);
        $filename = 'analytics_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($series): void {
            $output = fopen('php://output', 'w');
            // CSV header
            fputcsv($output, ['Date', 'Users', 'Revenue', 'Orders', 'Growth %']);

            $labels = $series['labels'] ?? [];
            $datasets = $series['datasets'] ?? [];

            foreach ($labels as $i => $label) {
                $row = [
                    $label,
                    $datasets['users'][$i] ?? 0,
                    $datasets['revenue'][$i] ?? 0,
                    $datasets['orders'][$i] ?? 0,
                    $datasets['growth'][$i] ?? 0,
                ];
                fputcsv($output, $row);
            }

            fclose($output);
        }, 200, $headers);
    }
}


