<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SegmentsController extends Controller
{
    /**
     * Breakdown of orders by status in a range.
     */
    public function statusBreakdown(): JsonResponse
    {
        $start = Carbon::parse(request('start', now()->subDays(29)->toDateString()))->startOfDay();
        $end = Carbon::parse(request('end', now()->toDateString()))->endOfDay();

        $rows = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(id) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return response()->json(['data' => $rows]);
    }
}


