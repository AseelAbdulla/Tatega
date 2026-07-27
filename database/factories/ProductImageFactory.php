<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
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
        'image_path' => fake()->imageUrl(),
        'is_main' => fake()->boolean(),
        'sort_order' => fake()->numberBetween(1, 5),
    ];
}
}
