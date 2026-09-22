<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; min-height: 100vh; align-items: center; justify-content: center; background: #f8fafc; margin: 0; }
        form { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); width: 100%; max-width: 360px; }
        label { display: block; font-size: 14px; margin-bottom: 6px; color: #334155; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 14px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #15803d; color: #fff; border: 0; border-radius: 8px; font-weight: 600; cursor: pointer; }
        h1 { margin: 0 0 1.25rem; text-align: center; font-size: 1.35rem; color: #0f172a; }
    </style>
</head>
<body>
    <form method="POST" action="{{ url('/ServiceLogin') }}">
        @csrf
        <h1>DEMO PSB</h1>
        <label>Username</label>
        <input type="text" name="username" required autocomplete="username">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
        <button type="submit">Log In</button>
    </form>
    @include('partials.session-guard')
</body>
</html>
