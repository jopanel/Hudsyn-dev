<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Hudsyn\User;

class AuthController extends Controller
{
    // Display the login form
    public function showLoginForm()
    {
        return view('hudsyn.login');
    }

    // Process the login submission
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find the user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check if the user exists and the password is correct
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Log in the user. (Make sure your auth guard is configured appropriately.)
            Auth::login($user);

            // Redirect to the dashboard or admin home
            return redirect()->intended('/hudsyn/dashboard');
        }

        // Return back with an error message if authentication fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('hudsyn.login');
    }

}
