<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renders(): void
    {
        $response = $this->get('/analytics');
        $response->assertOk();
        $response->assertSee('Analytics');
    }

    public function test_data_endpoint_returns_json(): void
    {
        User::factory()->create();
        Order::factory()->create(['status' => 'paid', 'paid_at' => now()]);

        $response = $this->getJson('/analytics/data?days=7');
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'metrics' => ['users', 'revenue', 'orders', 'growth'],
                'series' => ['labels', 'datasets'],
            ],
        ]);
    }
}


