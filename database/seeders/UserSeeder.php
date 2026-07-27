<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم رئيسي ثابت لتسجيل الدخول والاختبار
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '0500000000',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // إنشاء 10 مستخدمين عشوائيين إضافيين عبر الـ Factory
        User::factory()->count(10)->create();
    }
}
