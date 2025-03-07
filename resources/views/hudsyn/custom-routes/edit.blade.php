@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit Custom Route</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.custom-routes.update', $customRoute->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Route (e.g., /about-us):</label>
            <input type="text" name="route" value="{{ old('route', $customRoute->route) }}" required>
        </div>
        <div>
            <label>Content Type:</label>
            <select name="content_type" required>
                <option value="page" {{ old('content_type', $customRoute->content_type) == 'page' ? 'selected' : '' }}>Page</option>
                <option value="blog" {{ old('content_type', $customRoute->content_type) == 'blog' ? 'selected' : '' }}>Blog</option>
                <option value="press_release" {{ old('content_type', $customRoute->content_type) == 'press_release' ? 'selected' : '' }}>Press Release</option>
            </select>
        </div>
        <div>
            <label>Content ID:</label>
            <input type="number" name="content_id" value="{{ old('content_id', $customRoute->content_id) }}" required>
        </div>
        <button type="submit">Update Custom Route</button>
    </form>
@endsection
