<!DOCTYPE html>
<html>
<head>
    <title>Hudsyn Admin Login</title>
</head>
<body>
    <h1>Hudsyn Admin Login</h1>
    <form method="POST" action="{{ route('hudsyn.login.submit') }}">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" required autofocus>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color:red;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>
