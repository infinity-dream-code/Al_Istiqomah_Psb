<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    public function handle($request, \Closure $next, ...$guards)
    {
        // Session file kadang kosong di tengah navigasi — coba pulihkan dulu.
        if (!Auth::check()) {
            RestoreAuthFromCookie::attempt($request);
        }

        return parent::handle($request, $next, ...$guards);
    }

    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        Log::info('auth.unauthenticated_redirect', [
            'path' => $request->fullUrl(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'has_auth_cookie' => $request->cookies->has(RestoreAuthFromCookie::cookieName()),
            'has_staff_cookie' => $request->cookies->has(RestoreAuthFromCookie::staffCookieName()),
        ]);

        return '/ServiceLogin';
    }
}
