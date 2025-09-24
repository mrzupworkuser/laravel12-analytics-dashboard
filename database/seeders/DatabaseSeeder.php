<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(25)->create();

        // Create a healthy mix of orders; ~60% paid
        Order::factory()->count(300)->state(function () {
            return [];
        })->create();
    }
}


