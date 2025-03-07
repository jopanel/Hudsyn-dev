@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit User</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label>Role:</label>
            <select name="role" required>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>Editor</option>
            </select>
        </div>
        <div>
            <label>Password (leave blank to keep current password):</label>
            <input type="password" name="password">
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirmation">
        </div>
        <button type="submit">Update User</button>
    </form>
@endsection
