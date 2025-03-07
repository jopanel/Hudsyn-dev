@extends('hudsyn.layouts.app')

@section('content')
    <h1>Layouts</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('hudsyn.layouts.create') }}">Create New Layout</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Header File</th>
                <th>Footer File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($layouts as $layout)
                <tr>
                    <td>{{ $layout->id }}</td>
                    <td>{{ $layout->name }}</td>
                    <td>{{ $layout->header_file }}</td>
                    <td>{{ $layout->footer_file }}</td>
                    <td>
                        <a href="{{ route('hudsyn.layouts.edit', $layout->id) }}">Edit</a>
                        <form action="{{ route('hudsyn.layouts.destroy', $layout->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this layout?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
