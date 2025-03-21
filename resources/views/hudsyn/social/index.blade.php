@extends('hudsyn.layouts.app')

@section('content')
    <h1>Social Posts</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif


    <a href="{{ route('hudsyn.social.create') }}">Create New Social Post</a>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Text (Excerpt)</th>
                <th>Scheduled For</th>
                <th>Status</th>
                <th>Platforms</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($post->text_content, 50) }}</td>
                    <td>{{ $post->scheduled_for }}</td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>
                        @if($post->platforms)
                            {{ implode(', ', $post->platforms) }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('hudsyn.social.edit', $post->id) }}">Edit</a>
                        <a href="{{ route('hudsyn.social.preview', $post->id) }}">Preview</a>
                        <form action="{{ route('hudsyn.social.destroy', $post->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this social post?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
