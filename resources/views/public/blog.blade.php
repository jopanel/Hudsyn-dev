<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $blog->title }}</title>
    <meta name="description" content="{{ $blog->meta_description ?? '' }}">
    <meta name="keywords" content="{{ $blog->meta_keywords ?? '' }}">
</head>
<body>
    <article>
        <h1>{{ $blog->title }}</h1>
        <p>
            By {{ $blog->author->name ?? 'Unknown' }} 
            @if($blog->published_at)
                on {{ \Carbon\Carbon::parse($blog->published_at)->format('M d, Y') }}
            @endif
        </p>
        <div>
            {!! $blog->content !!}
        </div>
    </article>
</body>
</html>
