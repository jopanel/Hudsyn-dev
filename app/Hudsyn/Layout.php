<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    protected $table = 'hud_layouts';

    protected $fillable = [
        'name',
        'header_file',
        'footer_file'
    ];
}
