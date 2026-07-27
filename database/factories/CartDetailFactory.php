<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\CartDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartDetail>
 */
class CartDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
{
    return [
        'cart_id' => Cart::factory(),

        'product_id' => Product::factory(),

        'unit_id' => ProductUnit::factory(),

        'quantity' => fake()->numberBetween(1, 10),

        'price' => fake()->randomFloat(2, 10, 500),
    ];
}
}
