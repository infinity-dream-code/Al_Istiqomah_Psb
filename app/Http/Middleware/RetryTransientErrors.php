<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Retry GET hingga 2x untuk error transient + reconnect DB bila perlu.
 * Tidak mengubah auth / tidak redirect ke login atau home.
 */
class RetryTransientErrors
{
    private const MAX_RETRIES = 2;

    public function handle(Request $request, Closure $next): Response
    {
        $attempt = 0;

        while (true) {
            try {
                $response = $next($request);

                if (
                    $attempt < self::MAX_RETRIES
                    && $this->shouldRetryResponse($request, $response, $attempt)
                ) {
                    $this->prepareRetry($request, $attempt, $response);
                    $attempt++;
                    continue;
                }

                return $response;
            } catch (Throwable $e) {
                if ($attempt >= self::MAX_RETRIES || !$this->shouldRetryException($request, $e)) {
                    throw $e;
                }

                $this->prepareRetry($request, $attempt, null, $e);
                $attempt++;
            }
        }
    }

    private function prepareRetry(Request $request, int $attempt, ?Response $response = null, ?Throwable $e = null): void
    {
        $request->attributes->set('_transient_retried', $attempt + 1);

        Log::warning('transient.retry', [
            'attempt' => $attempt + 1,
            'path' => $request->path(),
            'status' => $response?->getStatusCode(),
            'error' => $e?->getMessage(),
        ]);

        if ($e && $this->isConnectionProblem($e)) {
            try {
                DB::reconnect();
            } catch (Throwable $ignored) {
            }
        } elseif ($response && $response->getStatusCode() >= 500) {
            try {
                DB::reconnect();
            } catch (Throwable $ignored) {
            }
        }

        usleep(200000 * ($attempt + 1));
    }

    private function shouldRetryException(Request $request, Throwable $e): bool
    {
        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        return $this->isTransient($e);
    }

    private function shouldRetryResponse(Request $request, Response $response, int $attempt): bool
    {
        if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if ($response->getStatusCode() < 500) {
            return false;
        }

        $exception = $response->exception ?? null;
        if ($exception instanceof Throwable) {
            return $this->isTransient($exception);
        }

        // GET 500 tanpa detail: anggap transient, retry
        return true;
    }

    private function isConnectionProblem(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'gone away')
            || str_contains($message, 'lost connection')
            || str_contains($message, 'too many connections')
            || str_contains($message, 'refused')
            || ($e instanceof QueryException && in_array((string) ($e->errorInfo[1] ?? ''), ['2006', '2013', '1040'], true));
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

        return $e->getPrevious() ? $this->isTransient($e->getPrevious()) : false;
    }
}
