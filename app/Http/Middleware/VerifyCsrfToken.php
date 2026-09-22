<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * @var array<int, string>
     */
    protected $except = [
        // keepalive GET tidak kena CSRF; biarkan kosong
    ];

    /**
     * Jangan biarkan 419 "mentah" — lempar exception yang di-handle Handler (silent retry).
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Pastikan session punya token valid untuk halaman retry
            if ($request->hasSession() && !$request->session()->token()) {
                $request->session()->regenerateToken();
            }

            throw $e;
        }
    }
}
