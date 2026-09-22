<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AppIdentity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cadangan auth lewat cookie — nama cookie + fingerprint unik per project
 * supaya tidak nabrak instalasi/project lain di host yang sama.
 */
class RestoreAuthFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $userId = $this->readUserId($request);
            if ($userId) {
                try {
                    $user = User::find($userId);
                } catch (\Throwable $e) {
                    $user = null;
                }

                if ($user) {
                    Auth::login($user, false);
                    Log::info('auth.restored_from_cookie', [
                        'user_id' => $userId,
                        'path' => $request->path(),
                        'app' => AppIdentity::id(),
                    ]);
                }
            }
        }

        $response = $next($request);

        if (Auth::check()) {
            $response->headers->setCookie(self::makeCookie((int) Auth::id()));
            // Hapus cookie generik lama yang sering nabrak antar project
            $response->headers->setCookie(Cookie::forget('psbku_uid'));
            $response->headers->setCookie(Cookie::forget('psbku_session'));
        }

        return $response;
    }

    public static function cookieName(): string
    {
        return AppIdentity::authCookie();
    }

    public static function makeCookie(int $userId): SymfonyCookie
    {
        $value = json_encode([
            'id' => $userId,
            'app' => AppIdentity::fingerprint(),
            'exp' => now()->addYear()->timestamp,
        ]);

        return Cookie::make(
            self::cookieName(),
            $value,
            60 * 24 * 365,
            '/',
            null, // host-only — JANGAN pakai domain induk .smartpayment.co.id
            false,
            true,
            false,
            'lax'
        );
    }

    public static function forgetCookie(): SymfonyCookie
    {
        return Cookie::forget(self::cookieName());
    }

    private function readUserId(Request $request): ?int
    {
        $raw = $request->cookie(self::cookieName());
        if (!$raw) {
            return null;
        }

        try {
            $data = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($data) || empty($data['id']) || empty($data['exp'])) {
                return null;
            }

            // Tolak cookie dari project lain (fingerprint beda)
            if (empty($data['app']) || !hash_equals(AppIdentity::fingerprint(), (string) $data['app'])) {
                Log::info('auth.cookie_rejected_foreign_app', [
                    'cookie' => self::cookieName(),
                ]);

                return null;
            }

            if ((int) $data['exp'] < time()) {
                return null;
            }

            return (int) $data['id'];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
