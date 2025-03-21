@extends('hudsyn.layouts.app')

@section('content')
    <h1>Edit Social Post</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.social.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Optional dropdown for selecting an existing blog post or press release -->
        <div>
            <label for="content">Select Existing Content (Optional):</label>
            <select name="content" id="content">
                <option value="">-- None --</option>
                @foreach($contents as $content)
                    <option value="{{ $content->type }}:{{ $content->id }}"
                        {{ ($post->content_type == $content->type && $post->content_id == $content->id) ? 'selected' : '' }}>
                        {{ ucfirst($content->type) }}: {{ $content->title }}
                    </option>
                @endforeach
            </select>
        </div>


        <!-- Text content -->
        <div>
            <label for="text_content">Post Text:</label>
            <textarea name="text_content" id="text_content" rows="5" required>{{ old('text_content', $post->text_content) }}</textarea>
        </div>

        <!-- File upload for a photo -->
        <div>
            <label for="image">Upload New Photo (Optional - Leave blank to keep current):</label>
            <input type="file" name="image" id="image" accept="image/*">
            @if($post->image_path)
                <p>Current Image: <img src="{{ asset($post->image_path) }}" alt="Current image" style="max-width:100px;"></p>
            @endif
        </div>

        <!-- Checkboxes for selecting social platforms -->
        <div>
            <p>Select Platforms:</p>
            @php
                $oldPlatforms = old('platforms', $post->platforms);
            @endphp
            <label><input type="checkbox" name="platforms[]" value="instagram" {{ in_array('instagram', $oldPlatforms) ? 'checked' : '' }}> Instagram</label>
            <label><input type="checkbox" name="platforms[]" value="x" {{ in_array('x', $oldPlatforms) ? 'checked' : '' }}> X (Twitter)</label>
            <label><input type="checkbox" name="platforms[]" value="linkedin" {{ in_array('linkedin', $oldPlatforms) ? 'checked' : '' }}> LinkedIn</label>
            <label><input type="checkbox" name="platforms[]" value="facebook" {{ in_array('facebook', $oldPlatforms) ? 'checked' : '' }}> Facebook</label>
        </div>

        <!-- Date/Time picker for scheduling the post -->
        <div>
            <label for="scheduled_for">Schedule Post For:</label>
            <input type="datetime-local" name="scheduled_for" id="scheduled_for" value="{{ old('scheduled_for', date('Y-m-d\TH:i', strtotime($post->scheduled_for))) }}" required>
        </div>

        <button type="submit">Update Social Post</button>
    </form>
@endsection
