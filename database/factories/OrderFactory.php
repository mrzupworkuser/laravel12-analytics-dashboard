<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'paid', 'refunded']);
        $paidAt = $status === 'paid' ? $this->faker->dateTimeBetween('-45 days', 'now') : null;

        return [
            'user_id' => User::factory(),
            'total' => $this->faker->randomFloat(2, 10, 500),
            'status' => $status,
            'paid_at' => $paidAt,
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'updated_at' => now(),
        ];
    }
}


