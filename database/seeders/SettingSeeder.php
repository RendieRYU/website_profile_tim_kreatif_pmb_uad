<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'team_photo', 'value' => null],
            ['key' => 'registrant_count', 'value' => '0'],
            ['key' => 'ig_followers', 'value' => '0'],
            ['key' => 'ig_reach', 'value' => '0'],
            ['key' => 'ig_engagement', 'value' => '0'],
            ['key' => 'tiktok_followers', 'value' => '0'],
            ['key' => 'tiktok_views', 'value' => '0'],
            ['key' => 'tiktok_engagement', 'value' => '0'],
            ['key' => 'social_media_display', 'value' => 'both'],
            ['key' => 'instagram_link', 'value' => 'https://www.instagram.com/pmb_uad/'],
            ['key' => 'tiktok_link', 'value' => 'https://www.tiktok.com/@pmb_uad'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }
    }
}
