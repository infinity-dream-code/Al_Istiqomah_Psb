<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AppIdentity;
use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cadangan auth host-only (signed, tidak lewat EncryptCookies).
 * Ada cookie terpisah untuk staff supaya login siswa tidak “mencuri” menu admin.
 */
class RestoreAuthFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        self::attempt($request);

        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $response->headers->setCookie(self::makeCookie((int) $user->id, (string) $user->role));
            if ($user->role !== 'peserta') {
                $response->headers->setCookie(self::makeStaffCookie((int) $user->id, (string) $user->role));
            }
            $response->headers->setCookie(Cookie::forget('psbku_uid'));
            $response->headers->setCookie(Cookie::forget('psbku_session'));
        }

        return $response;
    }

    /**
     * Pulihkan / koreksi auth dari cookie. Aman dipanggil berkali-kali.
     *
     * $context = request "halaman tujuan" untuk menentukan role yang dibutuhkan
     * (dipakai keepalive & halaman login). Cookie/session tetap dari $request.
     */
    public static function attempt(Request $request, ?Request $context = null): bool
    {
        $context = $context ?: $request;

        if (!self::shouldConsiderRestore($context)) {
            return Auth::check();
        }

        $requiredRole = self::requiredRole($context);
        $preferStaff = $requiredRole === 'admin' || self::isStaffPath($context);

        // Cadangan di dalam session (kalau guard key hilang tapi file session masih ada)
        if (!Auth::check()) {
            self::restoreFromSessionBackup($request, $requiredRole, $preferStaff);
        }

        $cookieUser = self::resolveCookieUser($request, $preferStaff);

        if (!Auth::check()) {
            if ($cookieUser && self::userMatchesRoute($cookieUser, $requiredRole, $preferStaff)) {
                self::loginFromCookie($request, $cookieUser, 'restored');
                return true;
            }

            return false;
        }

        if (!$cookieUser || (int) Auth::id() === (int) $cookieUser->id) {
            return true;
        }

        $sessionOk = self::userMatchesRoute(Auth::user(), $requiredRole, $preferStaff);
        $cookieOk = self::userMatchesRoute($cookieUser, $requiredRole, $preferStaff);
        $sessionIsStaff = Auth::user()->role !== 'peserta';
        $cookieIsPeserta = $cookieUser->role === 'peserta';

        if (!$sessionOk && $cookieOk && !($sessionIsStaff && $cookieIsPeserta)) {
            self::loginFromCookie($request, $cookieUser, 'corrected');
            return true;
        }

        return $sessionOk;
    }

    /**
     * Pulihkan auth seolah-olah user sedang membuka $path (path relatif, mis. "/pages/dashboard").
     */
    public static function attemptForPath(Request $request, ?string $path): bool
    {
        $context = self::contextForPath($path);
        if (!$context) {
            return Auth::check();
        }

        return self::attempt($request, $context);
    }

    private static function contextForPath(?string $path): ?Request
    {
        $path = AuthRedirect::safeRelativeUrl($path);
        if ($path === null) {
            return null;
        }

        try {
            $context = Request::create($path, 'GET');
            $route = app('router')->getRoutes()->match($context);
            $context->setRouteResolver(fn () => $route);

            return $context;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function cookieName(): string
    {
        return AppIdentity::authCookie();
    }

    public static function staffCookieName(): string
    {
        return AppIdentity::authCookie() . '_staff';
    }

    public static function makeCookie(int $userId, ?string $role = null): SymfonyCookie
    {
        return self::buildCookie(self::cookieName(), $userId, $role);
    }

    public static function makeStaffCookie(int $userId, ?string $role = null): SymfonyCookie
    {
        return self::buildCookie(self::staffCookieName(), $userId, $role);
    }

    public static function forgetCookie(): SymfonyCookie
    {
        return Cookie::forget(self::cookieName());
    }

    public static function forgetStaffCookie(): SymfonyCookie
    {
        return Cookie::forget(self::staffCookieName());
    }

    private static function buildCookie(string $name, int $userId, ?string $role = null): SymfonyCookie
    {
        if ($role === null && Auth::check() && (int) Auth::id() === $userId) {
            $role = (string) Auth::user()->role;
        }

        $payload = [
            'id' => $userId,
            'role' => $role,
            'app' => AppIdentity::fingerprint(),
            'exp' => now()->addYear()->timestamp,
        ];

        return Cookie::make(
            $name,
            self::signPayload($payload),
            60 * 24 * 365,
            '/',
            null,
            false,
            true,
            false,
            'lax'
        );
    }

    private static function loginFromCookie(Request $request, User $user, string $reason): void
    {
        Auth::login($user, false);

        if ($request->hasSession()) {
            // Pastikan login langsung tertulis — cegah request berikutnya masih kosong.
            $request->session()->put('auth_backup_uid', (int) $user->id);
            $request->session()->put('auth_backup_role', (string) $user->role);
        }

        Log::info('auth.' . $reason . '_from_cookie', [
            'user_id' => $user->id,
            'role' => $user->role,
            'path' => $request->path(),
        ]);
    }

    private static function restoreFromSessionBackup(Request $request, ?string $requiredRole, bool $preferStaff): void
    {
        if (!$request->hasSession()) {
            return;
        }

        $uid = (int) $request->session()->get('auth_backup_uid');
        if ($uid < 1) {
            return;
        }

        try {
            $user = User::find($uid);
        } catch (\Throwable $e) {
            return;
        }

        if (!$user || !self::userMatchesRoute($user, $requiredRole, $preferStaff)) {
            return;
        }

        Auth::login($user, false);
        Log::info('auth.restored_from_session_backup', [
            'user_id' => $user->id,
            'role' => $user->role,
            'path' => $request->path(),
        ]);
    }

    private static function shouldConsiderRestore(Request $request): bool
    {
        if ($request->is(
            'session/keepalive',
            'ServiceLogin',
            'ServiceLogout',
            'enroll',
            'enroll/*'
        )) {
            return false;
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($path === '/' || $path === '') {
            return false;
        }

        if (self::isAppPath($request)) {
            return true;
        }

        foreach (self::routeMiddleware($request) as $middleware) {
            if ($middleware === 'auth' || str_starts_with($middleware, 'role:')) {
                return true;
            }
        }

        return false;
    }

    private static function isAppPath(Request $request): bool
    {
        $path = '/' . ltrim($request->path(), '/');

        return str_starts_with($path, '/pages/')
            || str_starts_with($path, '/PmbMstPendaftarans/')
            || str_starts_with($path, '/PmbRef');
    }

    private static function isStaffPath(Request $request): bool
    {
        if (self::requiredRole($request) === 'peserta') {
            return false;
        }

        $path = '/' . ltrim($request->path(), '/');

        if ($path === '/pages/dashboard' || str_contains($path, 'lengkapi_data')) {
            return false;
        }

        return self::isAppPath($request);
    }

    private static function requiredRole(Request $request): ?string
    {
        foreach (self::routeMiddleware($request) as $middleware) {
            if (preg_match('/^role:(admin|peserta)$/', $middleware, $match)) {
                return $match[1];
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private static function routeMiddleware(Request $request): array
    {
        $route = $request->route();
        if (!$route) {
            return [];
        }

        $list = [];
        try {
            if (method_exists($route, 'gatherMiddleware')) {
                $list = $route->gatherMiddleware();
            } elseif (method_exists($route, 'middleware')) {
                $list = $route->middleware();
            }
        } catch (\Throwable $e) {
            $list = method_exists($route, 'middleware') ? (array) $route->middleware() : [];
        }

        return array_values(array_filter(array_map('strval', (array) $list)));
    }

    private static function userMatchesRoute(?User $user, ?string $requiredRole, bool $preferStaff): bool
    {
        if (!$user) {
            return false;
        }

        if ($requiredRole !== null) {
            return $user->role === $requiredRole;
        }

        if ($preferStaff) {
            return $user->role !== 'peserta';
        }

        return true;
    }

    private static function resolveCookieUser(Request $request, bool $preferStaff): ?User
    {
        $primary = self::readUserFrom($request, self::cookieName());
        $staff = self::readUserFrom($request, self::staffCookieName());

        if ($preferStaff) {
            if ($staff && $staff->role !== 'peserta') {
                return $staff;
            }
            if ($primary && $primary->role !== 'peserta') {
                return $primary;
            }

            return null;
        }

        return $primary ?: $staff;
    }

    private static function readUserFrom(Request $request, string $cookieName): ?User
    {
        $userId = self::readUserId($request, $cookieName);
        if (!$userId) {
            return null;
        }

        try {
            return User::find($userId);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function readUserId(Request $request, string $cookieName): ?int
    {
        $raw = $request->cookies->get($cookieName);
        if (!$raw || !is_string($raw)) {
            return null;
        }

        $data = self::parseCookieValue($raw);
        if (!is_array($data) || empty($data['id']) || empty($data['exp'])) {
            return null;
        }

        if (empty($data['app']) || !hash_equals(AppIdentity::fingerprint(), (string) $data['app'])) {
            Log::info('auth.cookie_rejected_foreign_app', [
                'cookie' => $cookieName,
            ]);

            return null;
        }

        if ((int) $data['exp'] < time()) {
            return null;
        }

        return (int) $data['id'];
    }

    private static function parseCookieValue(string $raw): ?array
    {
        // Format baru: payload.signature
        if (str_contains($raw, '.')) {
            [$encoded, $signature] = explode('.', $raw, 2);
            $expected = self::signEncoded($encoded);
            if (!hash_equals($expected, $signature)) {
                return null;
            }
            $json = base64_decode(strtr($encoded, '-_', '+/'), true);
            if ($json === false) {
                return null;
            }
            $data = json_decode($json, true);

            return is_array($data) ? $data : null;
        }

        // Format lama: JSON langsung / cookie Laravel terenkripsi
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }

        try {
            $decrypted = Crypt::decryptString($raw);
            $json = json_decode($decrypted, true);

            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function signPayload(array $payload): string
    {
        $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        return $encoded . '.' . self::signEncoded($encoded);
    }

    private static function signEncoded(string $encoded): string
    {
        return hash_hmac('sha256', $encoded, (string) config('app.key'));
    }
}
