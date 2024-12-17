<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Users or Products table is empty. Please seed them first.');
            return;
        }

        // Создаем первый тестовый заказ
        $firstOrder = Order::factory()->create([
            'user_id' => 1,
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => 9999,
            'email' => 'user@example.com',
            'full_name' => User::find(1)?->name,
            'phone' => '+79519383935',
            'address' => 'ONIX',
            'status' => 'pending'
        ]);

        $product = $products->first();
        OrderItem::create([
            'order_id' => $firstOrder->id,
            'product_id' => $product->id,
            'seller_id' => $product->seller_id,
            'quantity' => 1,
            'price' => 9999,
            'total' => 9999
        ]);

        $firstOrder->save();

        // Создаем остальные заказы
//        Order::factory()
//            ->count(20)
//            ->create([
//                'user_id' => fn () => $users->random()->id,
//            ]);

        $this->command->info('Orders have been created successfully with their items!');
    }
}
