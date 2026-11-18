<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Membaca view database v_saldo_va (bukan tabel aplikasi).
 * Kolom kunci VA dan nilai saldo bisa berbeda antar instalasi.
 */
class SaldoVa extends Model
{
    protected $table = 'v_saldo_va';

    public $timestamps = false;

    public $incrementing = false;

    protected $guarded = [];

    public static function findByVa(?string $vaNumber): ?self
    {
        $vaNumber = trim((string) $vaNumber);
        if ($vaNumber === '') {
            return null;
        }

        try {
            if (!Schema::hasTable('v_saldo_va')) {
                return null;
            }

            $columns = Schema::getColumnListing('v_saldo_va');
            $vaColumn = collect(['va_number', 'no_va', 'nomor_va', 'va', 'virtual_akun', 'norek'])
                ->first(fn ($col) => in_array($col, $columns, true));

            if (!$vaColumn) {
                return null;
            }

            return static::query()->where($vaColumn, $vaNumber)->first();
        } catch (\Throwable $e) {
            Log::warning('saldo_va.query_failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function amount(): ?float
    {
        foreach (['saldo', 'sisa_saldo', 'saldo_akhir', 'amount', 'nominal'] as $col) {
            if (array_key_exists($col, $this->attributes) && $this->attributes[$col] !== null && $this->attributes[$col] !== '') {
                return (float) $this->attributes[$col];
            }
        }

        return null;
    }
}
