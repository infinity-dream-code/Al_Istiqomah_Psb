<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Kalau SESSION_DRIVER=database tapi tabel sessions belum ada,
        // jangan crash — fallback ke file.
        if (config('session.driver') === 'database') {
            try {
                if (!Schema::hasTable('sessions')) {
                    config(['session.driver' => 'file']);
                }
            } catch (\Throwable $e) {
                config(['session.driver' => 'file']);
            }
        }

        // Pastikan cookie domain host-only (cegah nabrak antar subdomain project)
        if (config('session.domain') === '' || config('session.domain') === false) {
            config(['session.domain' => null]);
        }
    }
}
