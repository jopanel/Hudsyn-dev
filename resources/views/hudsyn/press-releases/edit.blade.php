@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit Press Release</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.press-releases.update', $pressRelease->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Title:</label>
            <input type="text" name="title" value="{{ old('title', $pressRelease->title) }}" required>
        </div>
        <div>
            <label>Slug:</label>
            <input type="text" name="slug" value="{{ old('slug', $pressRelease->slug) }}" required>
        </div>
        <div>
            <label>Content:</label>
            <textarea name="content" id="content" rows="10" required>{{ old('content', $pressRelease->content) }}</textarea>
        </div>
        <div>
            <label>Status:</label>
            <select name="status" required>
                <option value="draft" {{ old('status', $pressRelease->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $pressRelease->status) === 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>
        <div>
            <label>Author:</label>
            <select name="author_id" required>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id', $pressRelease->author_id) == $author->id ? 'selected' : '' }}>
                        {{ $author->name }} ({{ $author->email }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit">Update Press Release</button>
    </form>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content', {
            filebrowserImageBrowseUrl: '{{ url("hudsyn/files/gallery") }}',
            filebrowserImageUploadUrl: '{{ url("hudsyn/files/upload-image") }}?_token={{ csrf_token() }}'
        });
    </script>
@endsection 