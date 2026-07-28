<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        // تم تعديل البيانات بحيث تدعم اللغتين
        Feature::create([
            'title' => [
                'ar' => 'تسوق سريع',
                'en' => 'Fast Shipping',
            ],
            'description'=> [
                'ar' => 'اجعل منتجاتك تصل بسرعه',
                'en' => 'Get your products delivered quickly.',
            ],
            'icon' => 'default-icon.png', // أو أي اسم أيقونة موجود في جدولك
        ]);
    }
}