<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Hudsyn\Setting;

class TimezoneSettingSeeder extends Seeder
{
    public function run()
    {
        Setting::updateOrCreate(
            ['key' => 'system_timezone'],
            ['value' => 'America/Los_Angeles']
        );
    }
}
