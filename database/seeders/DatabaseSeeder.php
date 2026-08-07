<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,                // 1. المستخدمون أولاً (الجذر الأساسي)
            // RoleSeeder::class,                // 2. الأدوار والصلاحيات
            SettingSeeder::class,             // 3. إعدادات النظام العامة
            RolesAndPermissionsSeeder::class,
            AddressSeeder::class,             // 4. عناوين المستخدمين
            BannerSeeder::class,              // 5. البانرات الترويجية
            CategorySeeder::class,            // 6. التصنيفات
            FeatureSeeder::class,             // 7. الميزات
            PartnerSeeder::class,             // 8. الشركاء
            ProductSeeder::class,             // 9. المنتجات
            ProductImageSeeder::class,        // 10. صور المنتجات
            ProductUnitSeeder::class,         // 11. وحدات القياس للمنتجات
            ReviewSeeder::class,              // 12. تقييمات المنتجات
            CartSeeder::class,                // 13. سلات التسوق
            CartDetailSeeder::class,          // 14. تفاصيل سلات التسوق
            InternalNotificationSeeder::class, // 15. الإشعارات الداخلية
            OrderSeeder::class,               // 16. الطلبات
            OrderDetailSeeder::class,         // 17. تفاصيل الطلبات
        ]);
    }
}
