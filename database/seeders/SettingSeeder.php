<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'title' => 'SMA Islam PB Soedirman',
            'description' => 'Sekolah Islam unggulan dengan kurikulum berbasis teknologi.',
            'organization_name' => 'Yayasan Masjid PB Soedirman',
            'logo' => 'logo.png',
            'favicon' => 'pavicon.png',
            'quick_links' => json_encode([
                ['label' => 'Address', 'url' => 'https://maps.app.goo.gl/ijH4d1hAL3HsW9Qh6'],
                ['label' => 'Contact', 'url' => 'https://wa.me/6281234567890'],
                ['label' => 'Email', 'url' => 'mailto:ammarasysyakur723@gmail.com'],
            ]),
        ]);
        
    }
}
