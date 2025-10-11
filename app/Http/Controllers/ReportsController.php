<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReportsDataRequest;
use App\Http\Resources\ReportsSummaryResource;
use App\Http\Resources\TopCustomersResource;
use App\Services\ReportsService;
use Illuminate\Http\JsonResponse;

class ReportsController extends Controller
{
    public function __construct(private readonly ReportsService $reportsService)
    {}

    /**
     * Range-based revenue/orders summary.
     */
    public function summary(ReportsDataRequest $request): JsonResponse
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        
        if ($request->validated('start')) {
            $start = now()->parse($request->validated('start'))->startOfDay();
        }
        
        if ($request->validated('end')) {
            $end = now()->parse($request->validated('end'))->endOfDay();
        }

        $summary = $this->reportsService->getSummary($start, $end);

        return ReportsSummaryResource::make($summary)->response();
    }

    /**
     * Top customers by paid revenue within a range.
     */
    public function topCustomers(ReportsDataRequest $request): JsonResponse
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $limit = $request->validated('limit', 10);
        
        if ($request->validated('start')) {
            $start = now()->parse($request->validated('start'))->startOfDay();
        }
        
        if ($request->validated('end')) {
            $end = now()->parse($request->validated('end'))->endOfDay();
        }

        $customers = $this->reportsService->getTopCustomers($start, $end, $limit);

        return TopCustomersResource::make($customers)->response();
    }
}


