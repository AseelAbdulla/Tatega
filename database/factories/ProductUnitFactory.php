<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductUnit>
 */
class ProductUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
{
    return [
        'product_id' => Product::factory(),
        'unit_id' => fake()->numberBetween(1, 5),
        'price' => fake()->randomFloat(2, 5, 300),
        'stock' => fake()->numberBetween(0, 100),
    ];
}
}
