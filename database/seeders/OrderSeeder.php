<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'order_type' => 'normal',
            'status' => 'pending',
            'customer_name' => 'Test Customer',
            'customer_phone' => '0500000000',
            'customer_email' => 'test@example.com',
            'address_id' => null,
            'notes' => 'Test order notes',
            'shipping_building' => '123',
            'shipping_street' => 'the tahrer',
            'shipping_region' => 'Test tahrer',
            'shipping_city' => 'Test sanaa',
            'shipping_country' => 'Test yemen',
            'subtotal' => 300.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total_price' => 300.00,
            'rejection_reason' => null,
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'payment_recepit' => 'order/default.jpg',
        ]);
    }
}