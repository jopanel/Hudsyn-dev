@extends('hudsyn.layouts.app')

@section('content')
    <h1>Pages</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.pages.create') }}">Create New Page</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Homepage?</th>
                <th>Published At</th>
                <th>Static File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pages as $page)
            <tr>
                <td>{{ $page->id }}</td>
                <td>{{ $page->title }}</td>
                <td>{{ $page->slug }}</td>
                <td>{{ ucfirst($page->status) }}</td>
                <td>{{ $page->is_homepage ? 'Yes' : 'No' }}</td>
                <td>{{ $page->published_at ?? 'N/A' }}</td>
                <td>
                    @if($page->static_file_path)
                        <a href="{{ asset($page->static_file_path) }}" target="_blank">View</a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a href="{{ route('hudsyn.pages.edit', $page->id) }}">Edit</a>
                    <form action="{{ route('hudsyn.pages.destroy', $page->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this page?');">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
