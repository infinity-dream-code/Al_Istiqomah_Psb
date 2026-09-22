<?php

namespace App\Http\Middleware;

use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            Log::info('auth.role_redirect_login', [
                'path' => $request->path(),
                'need_role' => $role,
                'session_id' => $request->session()->getId(),
            ]);

            return redirect('/ServiceLogin');
        }

        if (Auth::user()->role !== $role) {
            // Salah role → dashboard role sendiri, bukan login/home
            Log::info('auth.role_mismatch_to_dashboard', [
                'path' => $request->path(),
                'need_role' => $role,
                'user_role' => Auth::user()->role,
                'user_id' => Auth::id(),
            ]);

            return redirect()->to(AuthRedirect::dashboardPath());
        }

        return $next($request);
    }
}
