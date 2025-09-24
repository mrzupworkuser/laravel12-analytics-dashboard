<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ReportsController extends Controller
{
    /**
     * Range-based revenue/orders summary.
     */
    public function summary(): JsonResponse
    {
        $start = Carbon::parse(request('start', now()->subDays(29)->toDateString()))->startOfDay();
        $end = Carbon::parse(request('end', now()->toDateString()))->endOfDay();

        $q = Order::query()->paid()->whereBetween('paid_at', [$start, $end]);

        $revenue = (float) $q->clone()->sum('total');
        $orders = (int) $q->clone()->count('id');

        return response()->json([
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'revenue' => round($revenue, 2),
            'orders' => $orders,
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0.0,
        ]);
    }

    /**
     * Top customers by paid revenue within a range.
     */
    public function topCustomers(): JsonResponse
    {
        $start = Carbon::parse(request('start', now()->subDays(29)->toDateString()))->startOfDay();
        $end = Carbon::parse(request('end', now()->toDateString()))->endOfDay();

        $rows = Order::query()
            ->paid()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('user_id, COALESCE(SUM(total),0) as revenue, COUNT(id) as orders')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json(['data' => $rows]);
    }
}


