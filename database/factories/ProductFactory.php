<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->numberBetween(100, 10000),
            'unit' => $this->faker->randomElement(['штука', 'упаковка']),
            'short_description' => $this->faker->sentence(10),
            'full_description' => $this->faker->paragraph(5),
            'image_preview' => $this->faker->imageUrl(300, 300, 'products', true),
            'images' => json_encode([
                $this->faker->imageUrl(400, 400, 'products', true),
                $this->faker->imageUrl(400, 400, 'products', true),
                $this->faker->imageUrl(400, 400, 'products', true),
            ]),
            'is_published' => $this->faker->boolean(70), // 70% вероятность публикации
        ];
    }
}
