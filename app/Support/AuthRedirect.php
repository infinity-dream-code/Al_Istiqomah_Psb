<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\MasterRole;

class AuthRedirect
{
    /**
     * Tujuan aman setelah login / recovery — BUKAN home publik `/` dan BUKAN login.
     */
    public static function dashboardPath($user = null): string
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return '/ServiceLogin';
        }

        if ($user->role === 'admin') {
            return '/pages/display/home';
        }

        if ($user->role === 'peserta') {
            return '/pages/dashboard';
        }

        try {
            $role = MasterRole::whereRaw('LOWER(nama_role) = ?', [strtolower((string) $user->role)])->first();
            if (!$role) {
                $compact = strtolower(preg_replace('/[\s\-]+/', '', (string) $user->role));
                $role = MasterRole::get()->first(function ($r) use ($compact) {
                    return strtolower(preg_replace('/[\s\-]+/', '', (string) $r->nama_role)) === $compact;
                });
            }
            if ($role) {
                return '/pages/display/home/' . Str::slug($role->nama_role, '-');
            }
        } catch (\Throwable $e) {
            // DB lagi bermasalah — jangan lempar error di recovery
        }

        return '/pages/display/home';
    }

    public static function isPublicBouncePath(?string $path): bool
    {
        $path = $path ?: '/';
        $path = '/' . ltrim(parse_url($path, PHP_URL_PATH) ?: $path, '/');
        if ($path === '//') {
            $path = '/';
        }

        return in_array($path, ['/', '/ServiceLogin', '/enroll', '/login', '/home'], true)
            || str_starts_with($path, '/ServiceLogin');
    }

    public static function normalizePath(?string $url): string
    {
        $path = parse_url((string) $url, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = (string) $url;
        }
        $path = '/' . ltrim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    /**
     * Halaman khusus siswa. Admin / role unit tidak boleh diarahkan ke sini.
     */
    public static function isPesertaPath(?string $url): bool
    {
        $path = self::normalizePath($url);

        return $path === '/pages/dashboard'
            || $path === '/PmbMstPendaftarans/lengkapi_data'
            || str_starts_with($path, '/peserta/');
    }

    /**
     * Ambil path + query dari URL internal. Null kalau URL ke host lain / tidak valid.
     */
    public static function safeRelativeUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || str_contains($url, '\\')) {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return null;
        }

        if (isset($parts['host'])) {
            $appHost = parse_url(url('/'), PHP_URL_HOST);
            $reqHost = request()?->getHost();
            if (!in_array(strtolower($parts['host']), array_filter([strtolower((string) $appHost), strtolower((string) $reqHost)]), true)) {
                return null;
            }
        } elseif (!str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return null;
        }

        $path = '/' . ltrim($parts['path'] ?? '/', '/');

        return $path . (isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '');
    }

    /**
     * URL login yang membawa halaman asal, supaya setelah login user kembali ke sana
     * walaupun session (tempat url.intended disimpan) ikut hilang.
     */
    public static function loginUrlFor(\Illuminate\Http\Request $request): string
    {
        $from = $request->isMethodSafe()
            ? $request->getPathInfo() . ($request->getQueryString() ? '?' . $request->getQueryString() : '')
            : url()->previous();
        $next = self::safeRelativeUrl($from);

        if ($next === null || self::isPublicBouncePath($next)) {
            return '/ServiceLogin';
        }

        return '/ServiceLogin?' . http_build_query(['next' => $next]);
    }

    /**
     * URL yang boleh dibuka user ini setelah login (halaman yang tadi ingin dikunjungi).
     */
    public static function userMayAccessPath($user, ?string $url): bool
    {
        if (!$user || !$url) {
            return false;
        }

        $path = self::normalizePath($url);
        if (self::isPublicBouncePath($path)) {
            return false;
        }

        $pesertaPath = self::isPesertaPath($path);
        if (($user->role ?? null) === 'peserta') {
            return $pesertaPath;
        }

        if ($pesertaPath) {
            return false;
        }

        return str_starts_with($path, '/pages/')
            || str_starts_with($path, '/PmbMstPendaftarans/');
    }
}
