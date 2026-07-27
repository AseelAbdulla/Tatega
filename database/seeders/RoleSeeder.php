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
        $roles = [
            ['name' => 'admin', 'display_name' => 'مدير النظام'],
            ['name' => 'customer', 'display_name' => 'عميل'],
            ['name' => 'importer', 'display_name' => 'مستورد'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}