@extends('hudsyn.layouts.app')

@section('content')
    <h1>Blog Posts</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.blog.create') }}">Create New Blog Post</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Author</th>
                <th>Published At</th>
                <th>Static File</th>
                <th>Public URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($blogs as $blog)
            <tr>
                <td>{{ $blog->id }}</td>
                <td>{{ $blog->title }}</td>
                <td>{{ $blog->slug }}</td>
                <td>{{ ucfirst($blog->status) }}</td>
                <td>{{ $blog->author ? $blog->author->name : 'N/A' }}</td>
                <td>{{ $blog->published_at ?? 'N/A' }}</td>
                <td>
                    @if($blog->status === 'published')
                        <a href="{{ asset('static/blog/' . $blog->slug . '.html') }}" target="_blank">
                            View Static
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($blog->status === 'published')
                        <a href="{{ url('blog/' . $blog->slug) }}" target="_blank">
                            View URL
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a href="{{ route('hudsyn.blog.edit', $blog->id) }}">Edit</a>
                    <form action="{{ route('hudsyn.blog.destroy', $blog->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this blog post?');">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
