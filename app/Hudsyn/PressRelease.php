<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class PressRelease extends Model
{
    protected $table = 'hud_press_releases';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'published_at',
        'author_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
