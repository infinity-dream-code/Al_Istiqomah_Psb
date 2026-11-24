<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Rename tabel SPMB daftar-ulang agar aman di DB 56 (SIKEU sudah punya `tagihan`).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tagihan') && !Schema::hasTable('tagihan_daful')) {
            // Hanya rename jika struktur SPMB (punya id_peserta), bukan SIKEU.tagihan.
            if (Schema::hasColumn('tagihan', 'id_peserta')) {
                Schema::rename('tagihan', 'tagihan_daful');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tagihan_daful') && !Schema::hasTable('tagihan')) {
            Schema::rename('tagihan_daful', 'tagihan');
        } elseif (Schema::hasTable('tagihan_daful') && Schema::hasTable('tagihan') && Schema::hasColumn('tagihan', 'siswa_id')) {
            // DB 56: SIKEU.tagihan sudah ada — jangan rename balik ke 'tagihan'.
        }
    }
};
