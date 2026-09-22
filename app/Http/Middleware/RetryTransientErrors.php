<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Retry GET sekali untuk error transient (DB lock/deadlock/gone away, session lock).
 * Tidak menampilkan halaman error bila retry berhasil.
 */
class RetryTransientErrors
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            if (!$this->shouldRetry($request, $e)) {
                throw $e;
            }

            $request->attributes->set('_transient_retried', true);

            usleep(150000);

            return $next($request);
        }
    }

    private function shouldRetry(Request $request, Throwable $e): bool
    {
        if ($request->attributes->get('_transient_retried')) {
            return false;
        }

        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        return $this->isTransient($e);
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
