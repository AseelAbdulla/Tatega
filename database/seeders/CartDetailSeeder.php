<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartDetail;

class CartDetailSeeder extends Seeder
{
    public function run(): void
    {
        CartDetail::create([
            'cart_id' => 1,
            'product_id' => 1,
            'unit_id' => 1,
            'quantity' => 2,
            'price' => 150.00, // إضافة السعر لتجنب الخطأ
        ]);
    }
}