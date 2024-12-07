<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Seller;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $sellers = Seller::get();

        if ($sellers->isEmpty()) {
            $this->command->info('No sellers found. Please seed sellers first.');
            return;
        }

        Product::factory(50)->create();
    }
}