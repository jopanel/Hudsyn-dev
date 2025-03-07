<?php

namespace App\Hudsyn;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'hud_users';

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password'
    ];

    // Relationships

    public function passwordTokens()
    {
        return $this->hasMany(PasswordToken::class, 'user_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    public function pressReleases()
    {
        return $this->hasMany(PressRelease::class, 'author_id');
    }
}
