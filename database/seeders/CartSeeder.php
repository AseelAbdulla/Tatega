<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if ($user) {
            Cart::create([
                'user_id' => $user->id,
            ]);
        } else {
            Cart::create([
                'user_id' => 1,
            ]);
        }
    }
}