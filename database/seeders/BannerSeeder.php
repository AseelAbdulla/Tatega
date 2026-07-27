<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'image_path' => 'banners/default.jpg',
            'slogan' => json_encode(['en' => 'Special Offer', 'ar' => 'عرض خاص']),
            'sort_order' => 1,
            'status' => 'active',
        ]);
    }
}