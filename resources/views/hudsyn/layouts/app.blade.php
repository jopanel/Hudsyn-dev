<!DOCTYPE html>
<html>
<head>
    <title>Hudsyn Admin</title>
    <style>
        /* Simple styling for demonstration */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 10px 20px; }
        nav ul { list-style: none; margin: 0; padding: 0; }
        nav ul li { display: inline-block; margin-right: 15px; }
        nav ul li a { color: #fff; text-decoration: none; }
        .container { padding: 20px; }
    </style>
</head>
<body>
    <header>
        <h1>Hudsyn Admin</h1>
        <nav>
            <ul>
                <li><a href="{{ route('hudsyn.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('hudsyn.users.index') }}">Users</a></li>
                <li><a href="{{ route('hudsyn.pages.index') }}">Pages</a></li>
                <li><a href="{{ route('hudsyn.blog.index') }}">Blog</a></li>
                <li><a href="{{ route('hudsyn.press-releases.index') }}">Press Releases</a></li>
                <li><a href="{{ route('hudsyn.custom-routes.index') }}">Custom Routes</a></li>
                <li><a href="{{ route('hudsyn.layouts.index') }}">Layouts</a></li>
                <li><a href="{{ route('hudsyn.settings.index') }}">Settings</a></li>
                <li><a href="{{ route('hudsyn.files.index') }}">Files</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer;">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
