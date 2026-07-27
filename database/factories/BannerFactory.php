<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'image_path' => fake()->imageUrl(),

        'slogan' => [
            'ar' => fake()->sentence(),
        ],

        'sort_order' => fake()->numberBetween(1, 10),

        'status' => fake()->randomElement([
            'active',
            'inactive',
        ]),
    ];
}
}
