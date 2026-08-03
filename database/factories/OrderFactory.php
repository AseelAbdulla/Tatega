<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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

            'shipping_building' =>  fake()->words(2, true),

            'shipping_street' =>  fake()->words(2, true),

            'shipping_region' =>  fake()->words(2, true),

            'shipping_city' =>  fake()->words(2, true),

            'shipping_country' =>  fake()->words(2, true),

            'notes' => fake()->optional()->sentence(),

            'subtotal' => fake()->randomFloat(2, 50, 1000),

            'discount' => fake()->randomFloat(2, 0, 100),

            'tax' => fake()->randomFloat(2, 0, 50),

            'total_price' => fake()->randomFloat(2, 50, 1200),

            'rejection_reason' => fake()->optional()->sentence(),

            /*
            | بيانات الدفع
            */

            'payment_method' => fake()->randomElement([
                'wallet',
                'cash_on_delivery',
            ]),

            'payment_status' => fake()->randomElement([
                'pending',
                'pending_review'
            ]),


            'payment_recepit' => 'order/default.jpg',
        ];
    }
}
