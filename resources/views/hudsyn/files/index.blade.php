@extends('hudsyn.layouts.app')

@section('content')
    <h1>File Uploads</h1>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Upload Form -->
    <form method="POST" action="{{ route('hudsyn.files.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Select File:</label>
            <input type="file" name="file" required>
        </div>
        <button type="submit">Upload File</button>
    </form>

    <hr>

    <!-- List of Files -->
    <h2>Uploaded Files</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Original Name</th>
                <th>File Name</th>
                <th>File Size</th>
                <th>MIME Type</th>
                <th>Preview / Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($files as $file)
                <tr>
                    <td>{{ $file->id }}</td>
                    <td>{{ $file->original_name }}</td>
                    <td>{{ $file->file_name }}</td>
                    <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                    <td>{{ $file->mime_type }}</td>
                    <td>
                        @if(str_starts_with($file->mime_type, 'image/'))
                            <img src="{{ asset($file->file_path) }}" alt="{{ $file->original_name }}" style="max-width:100px; max-height:100px;">
                        @endif
                        <br>
                        <a href="{{ asset($file->file_path) }}" target="_blank">View File</a>
                    </td>
                    <td>
                        <form action="{{ route('hudsyn.files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:red;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
