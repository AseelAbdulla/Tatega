<?php

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'icon' => fake()->word(),

        'title' => [
            'ar' => fake()->sentence(3),
        ],

        'description' => [
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
