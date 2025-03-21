<!DOCTYPE html>
<html>
<head>
    <title>Hudsyn Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 10px 20px; }
        nav ul { list-style: none; margin: 0; padding: 0; }
        nav ul li { display: inline-block; margin-right: 15px; }
        nav ul li a { color: #fff; text-decoration: none; }
        .container { padding: 20px; }
        .logout { float: right; }
    </style>
</head>
<body>
    <header>
        <h1>Hudsyn Dashboard</h1>
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
                <li class="logout">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background: none;border: none; color: #fff; cursor: pointer;">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Welcome, {{ auth()->user()->name }}</h2>
        <p>This is your dashboard. Use the navigation above to manage your content and settings.</p>
        <!-- You can add more dashboard widgets or statistics here -->
    </div>
</body>
</html>
