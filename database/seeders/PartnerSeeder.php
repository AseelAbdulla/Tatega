<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        Partner::create([
            'name' => 'Partner Name',
            'logo' => 'partners/default.png',
            'status' => 1,
        ]);
    }
}