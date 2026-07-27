<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الإعدادات الثابتة والأساسية للنظام
        $settings = [
            [
                'key' => 'site_name',
                'value' => ['ar' => 'متجرنا الإلكتروني'],
            ],
            [
                'key' => 'site_description',
                'value' => ['ar' => 'متجرك الأفضل لشراء المنتجات بأسعار مميزة'],
            ],
            [
                'key' => 'contact_phone',
                'value' => ['ar' => '+967770000000'],
            ],
            [
                'key' => 'contact_email',
                'value' => ['ar' => 'support@example.com'],
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        // إمكانية إضافة إعدادات عشوائية إضافية عبر الـ Factory إذا رغبت
        // Setting::factory()->count(3)->create();
    }
}