<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\DataPeserta;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use App\Models\MasterRole;

class LoginController extends Controller
{
    public function index()
    {
        try {
            return response()->view('auth.login');
        } catch (\Throwable $e) {
            report($e);

            // Jangan biarkan halaman login down — tampilkan view sederhana
            return response()->view('auth.login-fallback', [], 200);
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
            return back()->with('login_error', 'Username atau password salah.');
        }

        // Cek role dulu — jangan Auth::login lalu logout
        $redirectTo = $this->resolveRedirectPath($user);
        if ($redirectTo === null) {
            return back()->with('login_error', 'Role tidak dikenali.');
        }

        // Cek pembayaran peserta SEBELUM login
        if ($user->role === 'peserta') {
            $peserta = DataPeserta::where('id_user', $user->id)->first();

            if ($peserta) {
                $this->syncStatusBayar($peserta);

                if ((int) $peserta->status_paid === 0) {
                    $jwtKey = config('services.psb.jwt_key', '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4');
                    $payload = ['nomor_pendaftaran' => $peserta->no_pendaftaran];
                    $token = JWT::encode($payload, $jwtKey, 'HS256');
                    $link = url("PmbMstPendaftarans/success_enroll/{$token}");

                    return redirect('/ServiceLogin')->with([
                        'need_payment' => true,
                        'redirect_link' => $link,
                    ]);
                }
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended($redirectTo);
    }

    /**
     * Logout HANYA lewat method ini (tombol Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/ServiceLogin')->with('success', 'Anda berhasil logout.');
    }

    private function resolveRedirectPath(User $user): ?string
    {
        if ($user->role === 'admin') {
            return 'pages/display/home';
        }

        if ($user->role === 'peserta') {
            return 'pages/dashboard';
        }

        $role = MasterRole::where('nama_role', $user->role)->first();
        if ($role) {
            return 'pages/display/home/' . Str::slug($role->nama_role, '-');
        }

        return null;
    }

    /**
     * Sinkron status bayar dari WS. Gagal/timeout tidak mengubah status_paid lama.
     */
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
            // WS down — jangan ganggu login
        }
    }
}
