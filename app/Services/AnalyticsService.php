<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

/**
 * Metrics and chart data utilities.
 *
 * @author Manohar Zarkar
 */
class AnalyticsService
{
    /**
     * Headline metrics for the current month.
     *
     * @return array{
     *     users:int,
     *     revenue:float,
     *     orders:int,
     *     growth:float
     * }
     */
    public function getHeadlineMetrics(): array
    {
        $users = (int) User::query()->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $previousStart = (clone $startOfMonth)->subMonth()->startOfMonth();
        $previousEnd = (clone $startOfMonth)->subMonth()->endOfMonth();

        $revenue = (float) Order::query()
            ->paid()
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $orders = (int) Order::query()
            ->paid()
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->count('id');

        $previousRevenue = (float) Order::query()
            ->paid()
            ->whereBetween('paid_at', [$previousStart, $previousEnd])
            ->sum('total');

        $growth = $previousRevenue > 0.0
            ? (($revenue - $previousRevenue) / $previousRevenue) * 100.0
            : 0.0;

        return [
            'users' => $users,
            'revenue' => round($revenue, 2),
            'orders' => $orders,
            'growth' => round($growth, 2),
        ];
    }

    /**
     * Time-series data for charts, grouped per day.
     *
     * @param int $days Number of days to include in the series (inclusive of today)
     *
     * @return array{
     *     labels: string[],
     *     datasets: array<string, array<int, float|int>>
     * }
     */
    public function getTimeSeriesData(int $days = 14): array
    {
        $today = Carbon::today();
        $start = $today->copy()->subDays(max(1, $days - 1));

        // Pre-build the label map for all dates in range
        $dateCursor = $start->copy();
        $labels = [];
        $dateKeys = [];
        while ($dateCursor->lte($today)) {
            $labels[] = $dateCursor->format('M d');
            $dateKeys[] = $dateCursor->toDateString();
            $dateCursor->addDay();
        }

        // Orders per day (count) and revenue per day (sum)
        $ordersPerDay = Order::query()
            ->paid()
            ->whereBetween('paid_at', [$start->startOfDay(), $today->endOfDay()])
            ->selectRaw('DATE(paid_at) as d, COUNT(id) as c, SUM(total) as s')
            ->groupBy('d')
            ->pluck('c', 'd');

        $revenuePerDay = Order::query()
            ->paid()
            ->whereBetween('paid_at', [$start->startOfDay(), $today->endOfDay()])
            ->selectRaw('DATE(paid_at) as d, SUM(total) as s')
            ->groupBy('d')
            ->pluck('s', 'd');

        $usersPerDay = User::query()
            ->whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->selectRaw('DATE(created_at) as d, COUNT(id) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $usersSeries = [];
        $revenueSeries = [];
        $ordersSeries = [];
        $growthSeries = [];

        $previousRevenue = null;
        foreach ($dateKeys as $dk) {
            $orders = (int) ($ordersPerDay[$dk] ?? 0);
            $revenue = (float) ($revenuePerDay[$dk] ?? 0.0);
            $newUsers = (int) ($usersPerDay[$dk] ?? 0);

            $ordersSeries[] = $orders;
            $revenueSeries[] = round($revenue, 2);
            $usersSeries[] = $newUsers;

            if ($previousRevenue === null) {
                $growthSeries[] = 0.0;
            } else {
                $growthSeries[] = $previousRevenue > 0.0
                    ? round((($revenue - $previousRevenue) / $previousRevenue) * 100.0, 2)
                    : 0.0;
            }
            $previousRevenue = $revenue;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'users' => $usersSeries,
                'revenue' => $revenueSeries,
                'orders' => $ordersSeries,
                'growth' => $growthSeries,
            ],
        ];
    }
}


