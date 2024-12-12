<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'seller_id' => Seller::factory(),
            'order_number' => $this->faker->unique()->numerify('ORD###'),
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']),
        ];
    }
}
