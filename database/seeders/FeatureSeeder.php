<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        Feature::create([
            'title' => 'Fast Shipping',
            'description' => 'Get your products delivered quickly.',
            'icon' => 'default-icon.png', // أو أي اسم أيقونة موجود في جدولك
        ]);
    }
}