<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        Log::info('auth.unauthenticated_redirect', [
            'path' => $request->fullUrl(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        ]);

        return '/ServiceLogin';
    }
}
