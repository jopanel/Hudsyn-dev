@extends('hudsyn.layouts.app')

@section('content')
    <h1>Create Social Post</h1>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hudsyn.social.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Optional dropdown for selecting an existing blog post or press release -->
        <div>
            <label for="content">Select Existing Content (Optional):</label>
            <select name="content" id="content">
                <option value="">-- None --</option>
                @foreach($contents as $content)
                    <option value="{{ $content->type }}:{{ $content->id }}">
                        {{ ucfirst($content->type) }}: {{ $content->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Text content -->
        <div>
            <label for="text_content">Post Text:</label>
            <textarea name="text_content" id="text_content" rows="5" required>{{ old('text_content') }}</textarea>
        </div>

        <!-- File upload for a photo -->
        <div>
            <label for="image">Upload Photo (Optional):</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <!-- Checkboxes for selecting social platforms -->
        <div>
            <p>Select Platforms:</p>
            <label><input type="checkbox" name="platforms[]" value="instagram"> Instagram</label>
            <label><input type="checkbox" name="platforms[]" value="x"> X (Twitter)</label>
            <label><input type="checkbox" name="platforms[]" value="linkedin"> LinkedIn</label>
            <label><input type="checkbox" name="platforms[]" value="facebook"> Facebook</label>
        </div>

        <!-- Display the current timezone -->
        <div>
            <p>Current Timezone: <strong>{{ $timezone }}</strong></p>
            <p>Current Time: <strong>{{ $currentTime }}</strong></p>
        </div>

        <!-- Allow immediate posting -->
        <div>
            <input type="hidden" name="post_now" value="0"> <!-- Ensures a default value -->
            <label>
                <input type="checkbox" name="post_now" id="post_now" value="1" onchange="toggleSchedule()"> Post Immediately
            </label>
        </div>


        <!-- Date/Time picker for scheduling the post -->
        <div id="schedule_section">
            <label for="scheduled_for">Schedule Post For:</label>
            <input type="datetime-local" name="scheduled_for" id="scheduled_for" value="{{ old('scheduled_for') }}">
        </div>

        <button type="submit">Create Social Post</button>
    </form>

    <script>
        function toggleSchedule() {
            var checkbox = document.getElementById("post_now");
            var scheduleSection = document.getElementById("schedule_section");
            scheduleSection.style.display = checkbox.checked ? "none" : "block";
        }
    </script>
@endsection
