<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
 public function definition(): array
{
    $name = fake()->word();

    return [
        'name' => [
            'en' => ucfirst($name),
            'ar' => 'تصنيف ' . $name,
        ],
        'slug' => strtolower($name),
        'image' => fake()->imageUrl(),
    ];
}
}
