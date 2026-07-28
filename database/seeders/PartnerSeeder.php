<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        // تم التعديل بما يدعم اللغتين
        Partner::create([
            'name' => [
                'ar' => 'اسم العميل',
                'en' => 'Partner Name',
            ],
            'logo' => 'partners/default.png',
            'status' => 1,
        ]);
    }
}