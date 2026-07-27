<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    return [
        'category_id' => Category::factory(),

        'name' => [
            'ar' => fake()->words(3, true),
        ],

        'description' => [
            'ar' => fake()->sentence(),
        ],

        'sku' => fake()->unique()->bothify('SKU-####'),

        'base_price' => fake()->randomFloat(2, 10, 500),

        'has_discount' => fake()->boolean(),

        'discount_price' => fake()->optional()->randomFloat(2, 5, 400),

        'stock' => fake()->numberBetween(0, 100),

        'low_stock_threshold' => fake()->numberBetween(5, 20),

        'status' => 'active',
    ];
}
}
