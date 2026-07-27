<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        OrderDetail::create([
            'order_id' => 1,
            'product_id' => 1,
            'unit_id' => 1,
            'product_name_snapshot' => 'Test Product',
            'unit_name_snapshot' => 'Piece',
            'quantity' => 2,
            'unit_price' => 150.00,
            'total_price' => 300.00,
        ]);
    }
}