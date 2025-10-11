<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;

/**
 * Metrics and chart data utilities.
 *
 * @author Manohar Zarkar
 */
class AnalyticsService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrderRepository $orderRepository
    ) {}
    /**
     * Headline metrics for the current month.
     */
    public function getHeadlineMetrics(): array
    {
        $users = $this->userRepository->getTotalCount();
        
        $currentMonth = now()->startOfMonth();
        $previousMonth = $currentMonth->copy()->subMonth();

        $current = $this->orderRepository->getMonthlyMetrics(
            $currentMonth, 
            now()->endOfMonth()
        );
        
        $previous = $this->orderRepository->getMonthlyMetrics(
            $previousMonth, 
            $previousMonth->copy()->endOfMonth()
        );

        $growth = $previous['revenue'] > 0
            ? (($current['revenue'] - $previous['revenue']) / $previous['revenue']) * 100
            : 0;

        return [
            'users' => $users,
            'revenue' => round($current['revenue'], 2),
            'orders' => $current['orders'],
            'growth' => round($growth, 2),
        ];
    }

    /**
     * Time-series data for charts, grouped per day.
     */
    public function getTimeSeriesData(int $days = 14): array
    {
        $end = now();
        $start = $end->copy()->subDays($days - 1);

        $dateRange = collect()
            ->times($days, fn($i) => $start->copy()->addDays($i - 1))
            ->mapWithKeys(fn($date) => [$date->toDateString() => $date->format('M d')]);

        $orderData = $this->orderRepository->getDailyMetrics($start, $end);
        $userData = $this->userRepository->getDailyRegistrations($start, $end);

        $previousRevenue = null;
        $datasets = $dateRange->keys()->map(function ($date) use ($orderData, $userData, &$previousRevenue) {
            $orders = $orderData[$date]['orders'] ?? 0;
            $revenue = $orderData[$date]['revenue'] ?? 0;
            $users = $userData[$date] ?? 0;
            
            $growth = $previousRevenue > 0 && $previousRevenue !== null
                ? (($revenue - $previousRevenue) / $previousRevenue) * 100
                : 0;
            
            $previousRevenue = $revenue;
            
            return [
                'users' => (int) $users,
                'revenue' => round($revenue, 2),
                'orders' => (int) $orders,
                'growth' => round($growth, 2),
            ];
        });

        return [
            'labels' => $dateRange->values()->toArray(),
            'datasets' => [
                'users' => $datasets->pluck('users')->toArray(),
                'revenue' => $datasets->pluck('revenue')->toArray(),
                'orders' => $datasets->pluck('orders')->toArray(),
                'growth' => $datasets->pluck('growth')->toArray(),
            ],
        ];
    }
}


