<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Hudsyn\Setting;

class SocialCredentialsSeeder extends Seeder
{
    public function run()
    {
        $credentials = [
            'social_instagram_api_key' => '', 
            'social_instagram_ig_user_id' => '',
            'social_x_api_key'           => '',
            'social_linkedin_api_key'    => '',
            'social_linkedin_author_urn' => '',
            'social_facebook_api_key'    => '',
        ];

        foreach ($credentials as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
