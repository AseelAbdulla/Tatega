<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'category_id' => 1, // تأكد أن لديك قسم (Category) بالمعرف 1 تم إضافته في CategorySeeder
            'name' => json_encode(['en' => 'Sample Product', 'ar' => 'منتج تجريبي']),
            'description' => json_encode(['en' => 'This is a description', 'ar' => 'هذا وصف للمنتج']),
            'sku' => 'SKU-' . uniqid(),
            'base_price' => 150.00,
            'has_discount' => false,
            'stock' => 20,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);
    }
}