<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $sellers = Seller::all();

        if ($users->isEmpty() || $sellers->isEmpty()) {
            $this->command->warn('Users or Sellers table is empty. Please seed them first.');
            return;
        }

        Order::factory()->create([
            'user_id' => 1,
            'seller_id' => 1,
            'order_number' => 1,
            'total_amount' => 2500,
            'address' => 'ONIX',
            'status' => 'pending'
        ]);

        Order::factory()
            ->count(20)
            ->create([
                'user_id' => fn () => $users->random()->id,
                'seller_id' => fn () => $sellers->random()->id,
            ]);

        $this->command->info('20 orders have been created successfully!');
    }
}
