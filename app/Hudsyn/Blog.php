<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'hud_blog';

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
