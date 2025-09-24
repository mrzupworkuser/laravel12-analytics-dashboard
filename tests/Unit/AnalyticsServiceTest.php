<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_headline_metrics_and_series_are_generated(): void
    {
        User::factory()->count(3)->create();
        Order::factory()->count(10)->create(['status' => 'paid', 'paid_at' => now()]);

        $service = new AnalyticsService();

        $metrics = $service->getHeadlineMetrics();
        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('users', $metrics);
        $this->assertArrayHasKey('revenue', $metrics);
        $this->assertArrayHasKey('orders', $metrics);
        $this->assertArrayHasKey('growth', $metrics);

        $series = $service->getTimeSeriesData(7);
        $this->assertIsArray($series);
        $this->assertArrayHasKey('labels', $series);
        $this->assertArrayHasKey('datasets', $series);
        $this->assertCount(7, $series['labels']);
    }
}


