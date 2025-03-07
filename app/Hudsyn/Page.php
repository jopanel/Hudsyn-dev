<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'hud_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'is_homepage',
        'layout_header',
        'layout_footer',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'static_file_path',
        'published_at'
    ];
}
