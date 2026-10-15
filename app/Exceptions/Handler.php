<?php

namespace App\Exceptions;

use App\Support\AuthRedirect;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

    }

    public function render($request, Throwable $e)
    {
        // Harus sebelum parent::render(): di sana TokenMismatchException sudah diubah jadi
        // HttpException(419) sebelum callback renderable() dijalankan, jadi tidak pernah tertangkap.
        if ($e instanceof TokenMismatchException) {
            return $this->recoverFromCsrfMismatch($request);
        }

        $response = parent::render($request, $e);

        if ($request->expectsJson() || $request->wantsJson()) {
            return $response;
        }

        if ($response->getStatusCode() >= 500) {
            Log::error('http.500_soft_recover', [
                'path' => $request->fullUrl(),
                'auth' => Auth::check(),
                'user_id' => Auth::id(),
                'role' => Auth::user()?->role,
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            $fallback = Auth::check() ? AuthRedirect::dashboardPath() : '';

            if (!$request->isMethodSafe()) {
                return $this->backToForm($request, 'Data belum tersimpan karena terjadi gangguan. Isian Anda sudah dikembalikan, silakan coba simpan lagi.');
            }

            try {
                return response()->view('errors.soft-recover', [
                    'reloadUrl' => $request->fullUrl(),
                    'fallbackUrl' => $fallback,
                    'exception' => $e,
                ], 200);
            } catch (Throwable $viewError) {
                return response(
                    '<!DOCTYPE html><html><body><script>' .
                    'location.replace(' . json_encode($request->fullUrl()) . ');' .
                    '</script></body></html>',
                    200,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }
        }

        return $response;
    }

    /**
     * Kembali ke halaman form asal beserta isiannya; dashboard hanya bila asal tidak diketahui.
     */
    private function backToForm(Request $request, string $message)
    {
        $previous = AuthRedirect::safeRelativeUrl(url()->previous());
        $current = AuthRedirect::safeRelativeUrl($request->getRequestUri());

        if ($previous === null || $previous === $current || AuthRedirect::isPublicBouncePath($previous)) {
            $previous = Auth::check() ? AuthRedirect::dashboardPath() : '/';
        }

        try {
            $input = $request->except(['_token', '_csrf_retry', '_method', 'password', 'password_confirmation', 'current_password']);
            $input = array_filter($input, fn ($v) => !$v instanceof \Illuminate\Http\UploadedFile);
        } catch (Throwable $e) {
            $input = [];
        }

        return redirect()->to($previous)
            ->withInput($input)
            ->with('recover_error', $message);
    }

    private function recoverFromCsrfMismatch(Request $request)
    {
        // Pastikan ada token session
        if ($request->hasSession()) {
            if (!$request->session()->token()) {
                $request->session()->regenerateToken();
            }
        }

        $token = csrf_token();

        Log::info('csrf.recover', [
            'path' => $request->path(),
            'method' => $request->method(),
            'auth' => Auth::check(),
            'user_id' => Auth::id(),
            'retry' => $request->boolean('_csrf_retry'),
        ]);

        if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'message' => 'csrf_retry',
                'token' => $token,
                'retry' => !$request->boolean('_csrf_retry'),
            ], 419)->header('X-CSRF-TOKEN', $token);
        }

        // Sudah retry sekali: kembali ke form yang sedang diisi (jangan ke login / dashboard)
        if ($request->boolean('_csrf_retry')) {
            return $this->backToForm($request, 'Sesi diperbarui. Isian Anda sudah dikembalikan, silakan klik simpan sekali lagi.');
        }

        if (count($request->allFiles()) > 0) {
            return $this->backToForm($request, 'Sesi diperbarui. Silakan pilih ulang file lalu klik simpan sekali lagi.');
        }

        if (in_array($request->method(), ['GET', 'HEAD'], true)) {
            return redirect()->to($request->fullUrl());
        }

        // Auto-resubmit form dengan token baru (status 200, bukan halaman error 419)
        return response()->view('errors.csrf-retry', [
            'token' => $token,
            'action' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->except(['_token', '_csrf_retry']),
        ], 200);
    }
}
