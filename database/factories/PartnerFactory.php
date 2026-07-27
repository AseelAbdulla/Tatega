<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
{
    return [
        'name' => [
            'ar' => fake()->company(),
        ],

        'logo' => fake()->imageUrl(),

        'website_url' => fake()->optional()->url(),

        'sort_order' => fake()->numberBetween(1, 10),

        'status' => fake()->randomElement([
            'active',
            'inactive',
        ]),

        'lat' => fake()->optional()->latitude(),

        'lng' => fake()->optional()->longitude(),
    ];
}
}
