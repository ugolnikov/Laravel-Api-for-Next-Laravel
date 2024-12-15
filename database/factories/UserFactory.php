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
            'phone' => '+7' . $this->faker->numberBetween(900, 999) . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => 'customer',
        ];
    }
}
