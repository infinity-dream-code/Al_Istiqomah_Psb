<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // CSRF mismatch → recover diam-diam (refresh token + retry), tanpa pesan / tanpa redirect login
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            return $this->recoverFromCsrfMismatch($request);
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        // Pastikan halaman 500 custom (auto-reload sekali) dipakai untuk HTML server error
        if (
            !$request->expectsJson()
            && $response->getStatusCode() >= 500
            && $request->isMethodSafe()
        ) {
            return response()->view('errors.500', [
                'exception' => $e,
            ], 500);
        }

        return $response;
    }

    private function recoverFromCsrfMismatch(Request $request)
    {
        $token = csrf_token();

        if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'message' => 'csrf_retry',
                'token' => $token,
                'retry' => !$request->boolean('_csrf_retry'),
            ], 419)->header('X-CSRF-TOKEN', $token);
        }

        // Sudah pernah di-retry → kembali ke halaman sebelumnya tanpa flash pesan sesi
        if ($request->boolean('_csrf_retry')) {
            return redirect()->back()->withInput($request->except(['_token', '_csrf_retry']));
        }

        // Upload file tidak bisa di-replay aman → kembali diam-diam
        if (count($request->allFiles()) > 0) {
            return redirect()->back();
        }

        if (in_array($request->method(), ['GET', 'HEAD'], true)) {
            return redirect()->to($request->fullUrl());
        }

        return response()->view('errors.csrf-retry', [
            'token' => $token,
            'action' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->except(['_token', '_csrf_retry']),
        ], 200);
    }
}
