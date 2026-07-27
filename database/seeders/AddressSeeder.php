<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // هذا السطر سيقوم بإنشاء 50 عنواناً، ومع كل عنوان سيقوم بإنشاء مستخدم جديد تلقائياً
        Address::factory()->count(50)->create();
    }
}