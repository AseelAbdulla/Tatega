<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        ProductImage::create([
            'product_id' => 1, // تأكد أن لديك منتج بالمعرف 1
            'image_path' => 'products/sample.jpg', // أو اسم العمود الصحيح للصورة لديك
        ]);
    }
}