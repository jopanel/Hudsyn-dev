@extends('hudsyn.layouts.app')

@section('content')
    <h1>Preview Social Post</h1>
    
    <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
        <h2>Text Content</h2>
        <p>{{ $post->text_content }}</p>
        
        @if($post->image_path)
            <h2>Image</h2>
            <img src="{{ asset($post->image_path) }}" alt="Social Post Image" style="max-width:300px;">
        @endif

        <h2>Scheduled For</h2>
        <p>{{ $post->scheduled_for }}</p>

        <h2>Platforms</h2>
        <p>{{ $post->platforms ? implode(', ', $post->platforms) : 'None' }}</p>
    </div>

    <a href="{{ route('hudsyn.social.edit', $post->id) }}">Edit Post</a>
    <a href="{{ route('hudsyn.social.index') }}">Back to Social Posts</a>
@endsection
