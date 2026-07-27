<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $countries = ['Yemen', 'Saudi Arabia', 'UAE', 'Jordan', 'Egypt'];
        $cities = ['Sanaa', 'Riyadh', 'Dubai', 'Amman', 'Cairo'];
        $streets = ['King Fahd St', 'Al-Jumhuriya St', 'Zubairi St', 'Main Street', 'Tahrir St'];

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'country' => $countries[array_rand($countries)],
            'city' => $cities[array_rand($cities)],
            'region' => 'Central Region',
            'street' => $streets[array_rand($streets)],
            'building' => rand(1, 100),
            'notes' => 'Test address note',
        ];
    }
}