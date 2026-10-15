<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PSB {{ config('client.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(config('client.logo')) }}">
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
        <input type="hidden" name="next" value="{{ $next ?? '' }}">
        <h1>PSB {{ config('client.name') }}</h1>
        @php $loginError = session('login_error') ?: $errors->first(); @endphp
        @if ($loginError)
            <p style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;padding:10px 12px;border-radius:8px;font-size:14px;margin:0 0 14px;">{{ $loginError }}</p>
        @endif
        @if (session('success'))
            <p style="background:#f0fdf4;border:1px solid #86efac;color:#15803d;padding:10px 12px;border-radius:8px;font-size:14px;margin:0 0 14px;">{{ session('success') }}</p>
        @endif
        <label>Username</label>
        <input type="text" name="username" value="{{ old('username') }}" required autocomplete="username">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
        <button type="submit">Log In</button>
    </form>
    @include('partials.session-guard')
</body>
</html>
