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
