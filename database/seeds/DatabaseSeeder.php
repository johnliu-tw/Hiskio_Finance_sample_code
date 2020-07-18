<?php

use Illuminate\Database\Seeder;
use App\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Product::updateOrCreate(['id' => 1, 'name' => '測試商品1', 'price' => 200]);
        Product::updateOrCreate(['id' => 2, 'name' => '測試商品2', 'price' => 500]);
    }
}
