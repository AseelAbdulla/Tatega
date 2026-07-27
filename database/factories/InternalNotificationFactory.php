<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\InternalNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InternalNotification>
 */
class InternalNotificationFactory extends Factory
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

        'title' => [
            'ar' => fake()->sentence(3),
        ],

        'message' => [
            'ar' => fake()->paragraph(),
        ],

        'type' => fake()->randomElement([
            'order',
            'system',
            'promotion',
            'general',
        ]),

        'is_read' => fake()->boolean(),

        'sent_at' => fake()->optional()->dateTime(),
    ];
}
}
