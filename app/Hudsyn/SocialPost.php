<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $table = 'hud_social_posts';

    protected $fillable = [
        'text_content',
        'image_path',
        'content_type',
        'content_id',
        'tinyurl_id',
        'scheduled_for',
        'status',
        'platforms',
        'platform_results',
    ];

    protected $casts = [
        'platforms'        => 'array',
        'platform_results' => 'array',
        'scheduled_for'    => 'datetime',
    ];
}
