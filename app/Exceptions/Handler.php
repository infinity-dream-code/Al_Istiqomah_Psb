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

        $this->renderable(function (TokenMismatchException $e, Request $request) {
            return $this->recoverFromCsrfMismatch($request);
        });
    }

    public function render($request, Throwable $e)
    {
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
                return redirect()->to($fallback !== '' ? $fallback : url()->previous());
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

        // Sudah retry sekali: kembali diam-diam (jangan ke login)
        if ($request->boolean('_csrf_retry')) {
            if (Auth::check()) {
                return redirect()->to(AuthRedirect::dashboardPath());
            }

            return redirect()->back()->withInput($request->except(['_token', '_csrf_retry']));
        }

        if (count($request->allFiles()) > 0) {
            return redirect()->back();
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
