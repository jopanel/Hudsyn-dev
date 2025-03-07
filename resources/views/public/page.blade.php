<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $page->meta_title ?? $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
</head>
<body>
    <header>
        @if($page->layout_header)
            @include('layouts.headers.' . $page->layout_header)
        @else
            <h1>{{ $page->title }}</h1>
        @endif
    </header>
    <main>
        {!! $page->content !!}
    </main>
    <footer>
        @if($page->layout_footer)
            @include('layouts.footers.' . $page->layout_footer)
        @else
            <p>&copy; {{ date('Y') }} Hudsyn</p>
        @endif
    </footer>
</body>
</html>
