<?php

namespace App\Hudsyn;

use Illuminate\Database\Eloquent\Model;

class PasswordToken extends Model
{
    protected $table = 'hud_password_tokens';

    protected $fillable = [
        'user_id', 'token', 'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
