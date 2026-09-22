<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Retry GET sekali untuk error transient (DB lock/deadlock/gone away, session lock).
 *
 * Catatan Laravel 10+: exception di dalam pipeline sering sudah di-render jadi
 * Response 500, jadi kita cek status response juga — bukan hanya catch Throwable.
 */
class RetryTransientErrors
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (Throwable $e) {
            if (!$this->shouldRetryException($request, $e)) {
                throw $e;
            }

            return $this->retry($request, $next);
        }

        if ($this->shouldRetryResponse($request, $response)) {
            return $this->retry($request, $next);
        }

        return $response;
    }

    private function retry(Request $request, Closure $next): Response
    {
        $request->attributes->set('_transient_retried', true);
        usleep(150000);

        try {
            return $next($request);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    private function shouldRetryException(Request $request, Throwable $e): bool
    {
        if ($request->attributes->get('_transient_retried')) {
            return false;
        }

        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        return $this->isTransient($e);
    }

    private function shouldRetryResponse(Request $request, Response $response): bool
    {
        if ($request->attributes->get('_transient_retried')) {
            return false;
        }

        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if ($response->getStatusCode() < 500) {
            return false;
        }

        // Laravel Response::withException — pakai exception asli bila ada
        $exception = null;
        if (method_exists($response, 'exception')) {
            $exception = $response->exception;
        } elseif (property_exists($response, 'exception')) {
            $exception = $response->exception;
        }

        if ($exception instanceof Throwable) {
            return $this->isTransient($exception);
        }

        // Tanpa exception detail: tetap retry GET 500 sekali (transient sering sudah di-render)
        return true;
    }

    private function isTransient(Throwable $e): bool
    {
        if ($e instanceof QueryException) {
            $code = (string) ($e->errorInfo[1] ?? '');
            if (in_array($code, ['1213', '1205', '40001', '2006', '2013', '1040', '1053'], true)) {
                return true;
            }
        }

        $message = strtolower($e->getMessage());

        $needles = [
            'deadlock',
            'lock wait timeout',
            'server has gone away',
            'lost connection',
            'serialization failure',
            'too many connections',
            'try restarting transaction',
            'resource temporarily unavailable',
            'unable to acquire',
            'session has already been started',
            'failed to open stream: permission denied',
            'database is locked',
            'sqlstate[40p01]',
            'sqlstate[40001]',
            'sqlstate[hy000]',
        ];

        foreach ($needles as $needle) {
            if (str_contains($message, $needle)) {
                return true;
            }
        }

        if ($e->getPrevious()) {
            return $this->isTransient($e->getPrevious());
        }

        return false;
    }
}
