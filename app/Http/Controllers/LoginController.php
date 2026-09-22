<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\DataPeserta;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use App\Models\MasterRole;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->to(\App\Support\AuthRedirect::dashboardPath());
        }

        try {
            $html = view('auth.login')->render();

            return response($html);
        } catch (\Throwable $e) {
            report($e);

            try {
                return response(view('auth.login-fallback')->render());
            } catch (\Throwable $e2) {
                report($e2);

                $token = '';
                try {
                    $token = csrf_token();
                } catch (\Throwable $e3) {
                }

                return response(
                    '<!DOCTYPE html><html><body>' .
                    '<form method="POST" action="' . e(url('/ServiceLogin')) . '">' .
                    '<input type="hidden" name="_token" value="' . e($token) . '">' .
                    '<input name="username" placeholder="Username" required>' .
                    '<input type="password" name="password" placeholder="Password" required>' .
                    '<button type="submit">Log In</button></form></body></html>',
                    200,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])
            ->where('plain_password', $credentials['password'])
            ->first();

        if (!$user) {
            Log::warning('login.failed', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
            ]);

            return back()->with('login_error', 'Username atau password salah.');
        }

        $redirectTo = $this->resolveRedirectPath($user);
        if ($redirectTo === null) {
            Log::warning('login.unknown_role', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            return back()->with('login_error', 'Role tidak dikenali.');
        }

        if ($user->role === 'peserta') {
            $peserta = DataPeserta::where('id_user', $user->id)->first();

            if ($peserta) {
                $this->syncStatusBayar($peserta);

                if ((int) $peserta->status_paid === 0) {
                    $jwtKey = config('services.psb.jwt_key', '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4');
                    $payload = ['nomor_pendaftaran' => $peserta->no_pendaftaran];
                    $token = JWT::encode($payload, $jwtKey, 'HS256');
                    $link = url("PmbMstPendaftarans/success_enroll/{$token}");

                    Log::info('login.need_payment', [
                        'user_id' => $user->id,
                        'no_pendaftaran' => $peserta->no_pendaftaran,
                    ]);

                    return redirect('/ServiceLogin')->with([
                        'need_payment' => true,
                        'redirect_link' => $link,
                    ]);
                }
            }
        }

        // remember=false: tabel user tidak punya kolom remember_token
        Auth::login($user, false);
        $request->session()->regenerate();

        // Cookie cadangan supaya tidak ke-logout meski session sesaat hilang
        cookie()->queue(\App\Http\Middleware\RestoreAuthFromCookie::makeCookie((int) $user->id));

        $intendedRaw = $request->session()->get('url.intended');
        $target = $this->resolvePostLoginRedirect($request, $redirectTo);

        Log::info('login.success', [
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'auth_check' => Auth::check(),
            'session_id' => $request->session()->getId(),
            'url_intended_raw' => $intendedRaw,
            'redirect_to' => $target,
        ]);

        return redirect()->to($target);
    }

    public function logout(Request $request)
    {
        Log::info('logout', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/ServiceLogin')
            ->with('success', 'Anda berhasil logout.')
            ->withCookie(\App\Http\Middleware\RestoreAuthFromCookie::forgetCookie());
    }

    /**
     * Selalu ke dashboard role. Abaikan url.intended yang ke home/login/publik.
     */
    private function resolvePostLoginRedirect(Request $request, string $defaultPath): string
    {
        $default = '/' . ltrim($defaultPath, '/');
        $intended = $request->session()->pull('url.intended');

        if (!$intended) {
            return $default;
        }

        $path = parse_url($intended, PHP_URL_PATH);
        if ($path === null || $path === '') {
            $path = '/';
        }

        $blockedExact = ['/', '/ServiceLogin', '/enroll', '/login', '/home'];
        $blockedPrefix = ['/ServiceLogin', '/enroll'];

        if (in_array($path, $blockedExact, true)) {
            Log::info('login.ignore_intended', [
                'intended' => $intended,
                'reason' => 'public_or_login',
                'fallback' => $default,
            ]);

            return $default;
        }

        foreach ($blockedPrefix as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $default;
            }
        }

        // Hanya izinkan intended ke area app yang dilindungi
        $allowedPrefixes = ['/pages/', '/PmbMstPendaftarans/'];
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $intended;
            }
        }

        Log::info('login.ignore_intended', [
            'intended' => $intended,
            'reason' => 'not_app_area',
            'fallback' => $default,
        ]);

        return $default;
    }

    private function resolveRedirectPath(User $user): ?string
    {
        if ($user->role === 'admin') {
            return '/pages/display/home';
        }

        if ($user->role === 'peserta') {
            return '/pages/dashboard';
        }

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

        return null;
    }

    private function syncStatusBayar(DataPeserta $peserta): void
    {
        $jwtKey = config('services.psb.jwt_key', '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4');
        $payload = ['nomor_pendaftaran' => $peserta->no_pendaftaran];
        $tokenCek = JWT::encode($payload, $jwtKey, 'HS256');

        try {
            $response = Http::timeout(8)
                ->connectTimeout(5)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('http://10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
                    'token' => $tokenCek,
                    'method' => 'CekTagihan',
                ]);

            if (!$response->successful()) {
                return;
            }

            $result = $response->json();

            if (!isset($result['status'], $result['data'][0]) || (int) $result['status'] !== 200) {
                return;
            }

            $statusBayar = $result['data'][0]['StatusBayar'] ?? null;
            $tanggalBayar = $result['data'][0]['TanggalBayar'] ?? null;

            if ($statusBayar === null) {
                return;
            }

            if ((string) $statusBayar === '1') {
                $peserta->status_paid = 1;
                if ($tanggalBayar) {
                    $peserta->tgl_bayar_daftar = $tanggalBayar;
                }
                $peserta->save();
            } elseif ((string) $statusBayar === '0' && (int) $peserta->status_paid !== 1) {
                $peserta->status_paid = 0;
                $peserta->save();
            }
        } catch (\Throwable $e) {
            Log::warning('login.sync_bayar_failed', [
                'peserta_id' => $peserta->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
