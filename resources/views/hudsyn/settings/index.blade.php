@extends('hudsyn.layouts.app')

@section('content')
    <h1>Global Settings</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.settings.create') }}">Add New Setting</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Key</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->id }}</td>
                    <td>{{ $setting->key }}</td>
                    <td>{{ $setting->value }}</td>
                    <td>
                        <a href="{{ route('hudsyn.settings.edit', $setting->id) }}">Edit</a>
                        <form action="{{ route('hudsyn.settings.destroy', $setting->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this setting?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
