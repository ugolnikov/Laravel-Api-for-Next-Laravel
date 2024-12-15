<?php

namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), 
            'remember_token' => Str::random(10),
            'company_name' => $this->faker->company(),
            'inn' => $this->faker->numerify('############'),
            'address' => $this->faker->address,
            'phone' => '+7' . $this->faker->numberBetween(900, 999) . $this->faker->numerify('#######'),
            'logo' => $this->faker->imageUrl(200, 200, 'seller', true),
        ];
    }
}
