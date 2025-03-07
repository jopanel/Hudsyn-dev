@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit Page</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.pages.update', $page->id) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Title:</label>
            <input type="text" name="title" value="{{ old('title', $page->title) }}" required>
        </div>
        <div>
            <label>Slug:</label>
            <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" required>
        </div>
        <div>
            <label>Content:</label>
            <textarea name="content" id="content" rows="10" required>{{ old('content', $page->content) }}</textarea>
        </div>
        <div>
            <label>Status:</label>
            <select name="status" required>
                <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>
        <div>
            <label>Set as Homepage:</label>
            <input type="checkbox" name="is_homepage" value="1" {{ old('is_homepage', $page->is_homepage) ? 'checked' : '' }}>
        </div>
        <div>
            <label>Layout Header:</label>
            <input type="text" name="layout_header" value="{{ old('layout_header', $page->layout_header) }}">
        </div>
        <div>
            <label>Layout Footer:</label>
            <input type="text" name="layout_footer" value="{{ old('layout_footer', $page->layout_footer) }}">
        </div>
        <div>
            <label>Meta Title:</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}">
        </div>
        <div>
            <label>Meta Description:</label>
            <textarea name="meta_description" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
        </div>
        <div>
            <label>Meta Keywords:</label>
            <textarea name="meta_keywords" rows="3">{{ old('meta_keywords', $page->meta_keywords) }}</textarea>
        </div>
        <button type="submit">Update Page</button>
    </form>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content', {
            filebrowserImageBrowseUrl: '{{ url("hudsyn/files/gallery") }}',
            filebrowserImageUploadUrl: '{{ url("hudsyn/files/upload-image") }}?_token={{ csrf_token() }}'
        });
    </script>
@endsection