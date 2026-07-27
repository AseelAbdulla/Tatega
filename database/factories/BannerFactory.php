<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        $titles = ['Special Offer', 'New Collection', 'Summer Sale', 'Discount 50%'];

        return [
            'image' => 'banners/default.jpg',
            'title' => $titles[array_rand($titles)],
            'description' => 'This is a description for the banner element.',
            'sort_order' => rand(1, 10),
            'status' => 1,
        ];
    }
}