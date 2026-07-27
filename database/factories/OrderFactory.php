<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
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

        'order_type' => 'normal',

        'status' => fake()->randomElement([
            'pending',
            'completed',
            'cancelled'
        ]),

        'customer_name' => fake()->name(),

        'customer_phone' => fake()->phoneNumber(),

        'customer_email' => fake()->safeEmail(),

        'address_id' => Address::factory(),

        'notes' => fake()->optional()->sentence(),

        'subtotal' => fake()->randomFloat(2, 50, 1000),

        'discount' => fake()->randomFloat(2, 0, 100),

        'tax' => fake()->randomFloat(2, 0, 50),

        'total_price' => fake()->randomFloat(2, 50, 1200),

        'rejection_reason' => fake()->optional()->sentence(),
    ];
}
}
