<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),

            'product_id' => Product::factory(),

            'unit_id' => ProductUnit::factory(),

            'product_name_snapshot' => fake()->words(2, true),

            'unit_name_snapshot' => fake()->randomElement([
                'كيلو',
                'نصف كيلو',
                '250 جرام',
                'علبة',
                'كرتون'
            ]),

            'quantity' => fake()->numberBetween(1, 10),

            'unit_price' => fake()->randomFloat(2, 10, 500),

            'total_price' => fake()->randomFloat(2, 20, 2000),
        ];
    }
}
