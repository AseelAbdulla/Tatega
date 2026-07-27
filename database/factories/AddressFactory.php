<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'country' => fake()->country(),
        'city' => fake()->city(),
        'region' => fake()->state(),
        'street' => fake()->streetName(),
        'building' => fake()->buildingNumber(),
        'notes' => fake()->optional()->sentence(),
    ];
}
}
