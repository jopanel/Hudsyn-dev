@extends('hudsyn.layouts.app')

@section('content')
    <h1>Press Releases</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.press-releases.create') }}">Create New Press Release</a>
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
        @foreach($pressReleases as $pressRelease)
            <tr>
                <td>{{ $pressRelease->id }}</td>
                <td>{{ $pressRelease->title }}</td>
                <td>{{ $pressRelease->slug }}</td>
                <td>{{ ucfirst($pressRelease->status) }}</td>
                <td>{{ $pressRelease->author ? $pressRelease->author->name : 'N/A' }}</td>
                <td>{{ $pressRelease->published_at ?? 'N/A' }}</td>
                <td>
                    @if($pressRelease->status === 'published')
                        <a href="{{ asset('static/press/' . $pressRelease->slug . '.html') }}" target="_blank">
                            View Static
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($pressRelease->status === 'published')
                        <a href="{{ url('press/' . $pressRelease->slug) }}" target="_blank">
                            View URL
                        </a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a href="{{ route('hudsyn.press-releases.edit', $pressRelease->id) }}">Edit</a>
                    <form action="{{ route('hudsyn.press-releases.destroy', $pressRelease->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this press release?');">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
