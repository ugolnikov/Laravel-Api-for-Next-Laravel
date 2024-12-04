<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Получаем случайного продавца (предполагается, что продавцы уже созданы)
        $sellers = User::where('role', 'seller')->pluck('id');

        if ($sellers->isEmpty()) {
            $this->command->info('No sellers found. Please seed sellers first.');
            return;
        }

        // Генерация продуктов
        Product::factory(50)->create([
            'seller_id' => $sellers->random(),
        ]);
    }
}