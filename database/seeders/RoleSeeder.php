<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الأدوار الأساسية للنظام
        // تم تعديل نوع البيانات بحيث يدعم اللغتن
        $roles = [
            [
                'name' => 'admin',
                'display_name' => [
                    'ar' => 'مدير النظام',
                    'en' => 'Administrator',
                ],
            ],
            [
                'name' => 'customer',
                'display_name' => [
                    'ar' => 'عميل',
                    'en' => 'Customer',
                ],
            ],
            [
                'name' => 'importer',
                'display_name' => [
                    'ar' => 'مستورد',
                    'en' => 'Importer',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
