<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Hudsyn\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create an initial admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'), // Be sure to change this to a secure password
            'role'     => 'admin',
        ]);
    }
}
