<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $users = User::all();
        return view('hudsyn.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('hudsyn.users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:hud_users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,editor',
        ]);

        // Create the user
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('hudsyn.users.index')
                         ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('hudsyn.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate inputs. If email is changed, ensure it's unique.
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:hud_users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:admin,editor',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('hudsyn.users.index')
                         ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Prevent deletion of the currently logged-in user
        if (auth()->id() == $user->id) {
            return redirect()->route('hudsyn.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }
        $user->delete();

        return redirect()->route('hudsyn.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
