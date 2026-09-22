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

        // Jangan tutup error asli saat APP_DEBUG=true (biar ketahuan penyebabnya)
        if (config('app.debug')) {
            return $response;
        }

        // Production: halaman 500 custom (auto-reload) untuk GET HTML server error
        if (
            !$request->expectsJson()
            && $response->getStatusCode() >= 500
            && $request->isMethodSafe()
        ) {
            try {
                return response()->view('errors.500', [
                    'exception' => $e,
                ], 500);
            } catch (Throwable $viewError) {
                return response(
                    '<!DOCTYPE html><html><head><meta charset="UTF-8"><title></title></head><body>' .
                    '<script>location.reload()</script></body></html>',
                    500,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }
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

        if ($request->boolean('_csrf_retry')) {
            return redirect()->back()->withInput($request->except(['_token', '_csrf_retry']));
        }

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
