<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'hud_settings';

    protected $fillable = [
        'key',
        'value'
    ];
}
