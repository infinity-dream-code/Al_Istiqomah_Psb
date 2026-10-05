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

        // Selalu host-only. Domain induk (.smartpayment.co.id) bikin session
        // kebaca project lain, lalu menu admin kebuka sebagai siswa.
        config(['session.domain' => null]);

        $cookie = (string) config('session.cookie');
        if ($cookie !== '' && !str_ends_with($cookie, '_honly')) {
            // Nama baru: cookie session lama (domain induk) tidak ikut terbawa.
            config(['session.cookie' => $cookie . '_honly']);
        }
    }
}
