<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем тестового продавца с известными данными
        Seller::factory()->create([
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'company_name' => 'Test Company',
            'inn' => '123456789012',
            'is_verify' => true
        ]);

        // Создаем остальных случайных продавцов
        Seller::factory()->count(9)->create();

        $this->command->info('10 sellers have been created successfully!');
    }
}
