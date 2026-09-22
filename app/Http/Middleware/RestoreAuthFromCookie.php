<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cadangan auth lewat cookie (dienkripsi framework).
 * Kalau session sesaat hilang, user tidak ditendang ke login.
 * Cookie hanya dihapus saat Logout.
 */
class RestoreAuthFromCookie
{
    public const COOKIE = 'psbku_uid';

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $userId = $this->readUserId($request);
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    Auth::login($user, false);
                    Log::info('auth.restored_from_cookie', [
                        'user_id' => $userId,
                        'path' => $request->path(),
                    ]);
                }
            }
        }

        $response = $next($request);

        if (Auth::check()) {
            $response->headers->setCookie(self::makeCookie((int) Auth::id()));
        }

        return $response;
    }

    public static function makeCookie(int $userId): SymfonyCookie
    {
        $value = json_encode([
            'id' => $userId,
            'exp' => now()->addYear()->timestamp,
        ]);

        return Cookie::make(
            self::COOKIE,
            $value,
            60 * 24 * 365,
            '/',
            null,
            false,
            true,
            false,
            'lax'
        );
    }

    public static function forgetCookie(): SymfonyCookie
    {
        return Cookie::forget(self::COOKIE);
    }

    private function readUserId(Request $request): ?int
    {
        $raw = $request->cookie(self::COOKIE);
        if (!$raw) {
            return null;
        }

        try {
            $data = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($data) || empty($data['id']) || empty($data['exp'])) {
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
