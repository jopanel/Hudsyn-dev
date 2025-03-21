<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class TinyUrl extends Model
{
    protected $table = 'hud_tinyurl';

    protected $fillable = [
        'short_code',
        'original_url',
    ];
}
