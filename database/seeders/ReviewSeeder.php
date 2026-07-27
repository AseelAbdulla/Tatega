<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        // التأكد من وجود منتجات ومستخدمين قبل توزيع التقييمات
        if ($products->count() > 0 && $users->count() > 0) {
            $products->each(function ($product) use ($users) {
                // إنشاء تقييم مباشر لكل منتج
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $users->random()->id,
                    'rating' => 5,
                    'comment' => 'Great product!',
                ]);
            });
        } else {
            Review::create([
                'product_id' => 1,
                'user_id' => 1,
                'rating' => 5,
                'comment' => 'Great product!',
            ]);
        }
    }
}