<?php

namespace Database\Seeders;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class SellerSeeder extends Seeder
{
    public function run()
    {
        // Создаем 10 продавцов
        User::factory()->count(10)->create([
            'role' => 'seller',
            'email' => function ($user) {
                return 'seller' . Str::random(5) . '@example.com'; 
            },
        ]);

        $this->command->info('10 sellers have been created successfully!');
    }
}
