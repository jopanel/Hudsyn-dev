@extends('hudsyn.layouts.app')

@section('content')
    <h1>Custom Routes</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.custom-routes.create') }}">Create New Custom Route</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Route</th>
                <th>Content Type</th>
                <th>Content ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customRoutes as $customRoute)
                <tr>
                    <td>{{ $customRoute->id }}</td>
                    <td>{{ $customRoute->route }}</td>
                    <td>{{ ucfirst($customRoute->content_type) }}</td>
                    <td>{{ $customRoute->content_id }}</td>
                    <td>
                        <a href="{{ route('hudsyn.custom-routes.edit', $customRoute->id) }}">Edit</a>
                        <form action="{{ route('hudsyn.custom-routes.destroy', $customRoute->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this custom route?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
