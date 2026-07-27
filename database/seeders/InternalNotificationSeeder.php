<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InternalNotification;

class InternalNotificationSeeder extends Seeder
{
    public function run(): void
    {
        InternalNotification::create([
            'user_id' => 1,
            'type' => 'system', // إضافة نوع الإشعار لتجاوز قيد الـ NOT NULL
            'title' => json_encode(['en' => 'Welcome', 'ar' => 'أهلاً بك']),
            'message' => json_encode(['en' => 'Welcome to our platform', 'ar' => 'مرحباً بك في منصتنا']),
            'is_read' => false,
        ]);
    }
}