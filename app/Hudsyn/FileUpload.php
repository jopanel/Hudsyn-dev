<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'hud_files';

    protected $fillable = [
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type'
    ];
}
