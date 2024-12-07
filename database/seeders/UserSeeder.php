<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем тестового пользователя
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        // Создаем остальных случайных пользователей
        User::factory()->count(9)->create();

        $this->command->info('10 users have been created successfully!');
    }
}
