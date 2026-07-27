<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => 'User ' . rand(1, 100),
            'email' => 'user_' . rand(1, 1000) . '@example.com',
            'phone' => '05' . rand(10000000, 99999999),
            'password' => Hash::make('password'),
            'status' => 'active',
        ];
    }
}