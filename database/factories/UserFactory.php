<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            // 'email_verified_at' => now(),
            'password' => bcrypt('password'), // Используется простой пароль
            'remember_token' => Str::random(10),
            'role' => 'customer', // По умолчанию роль - покупатель
        ];
    }

    public function seller() // Метод для создания продавца
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'seller',
            ];
        });
    }
}
