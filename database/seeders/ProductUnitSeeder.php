<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductUnit;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        ProductUnit::create([
            'product_id' => 1,
            'unit_name' => json_encode(['en' => 'Piece', 'ar' => 'قطعة']),
            'price' => 150.00,
            'stock' => 20,
        ]);
    }
}