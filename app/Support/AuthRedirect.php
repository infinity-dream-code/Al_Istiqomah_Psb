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
}
