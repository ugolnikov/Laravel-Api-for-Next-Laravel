<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'email' => $this->faker->email,
            'full_name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['pending', 'shipped', 'completed', 'cancelled']),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::inRandomOrder()->limit(rand(1, 5))->get();

            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $total = $price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total
                ]);
            }

            $order->total_amount = $order->items->sum('total');
            $order->save();
        });
    }
}
