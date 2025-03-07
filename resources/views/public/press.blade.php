<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $pressRelease->title }}</title>
    <meta name="description" content="{{ $pressRelease->meta_description ?? '' }}">
    <meta name="keywords" content="{{ $pressRelease->meta_keywords ?? '' }}">
</head>
<body>
    <article>
        <h1>{{ $pressRelease->title }}</h1>
        <p>
            By {{ $pressRelease->author->name ?? 'Unknown' }}
            @if($pressRelease->published_at)
                on {{ \Carbon\Carbon::parse($pressRelease->published_at)->format('M d, Y') }}
            @endif
        </p>
        <div>
            {!! $pressRelease->content !!}
        </div>
    </article>
</body>
</html>
