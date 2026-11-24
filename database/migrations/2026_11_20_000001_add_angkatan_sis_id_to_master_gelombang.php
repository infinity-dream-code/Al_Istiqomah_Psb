<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Link master_gelombang ke tabel SIKEU di DB yang sama (56):
 * - angkatan_id     → angkatan      (gelombang)
 * - angkatan_sis_id → angkatansis   (tahun angkatan)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_gelombang', function (Blueprint $table) {
            if (!Schema::hasColumn('master_gelombang', 'angkatan_id')) {
                $table->unsignedInteger('angkatan_id')->nullable()->after('tahun_akademik');
            }
            if (!Schema::hasColumn('master_gelombang', 'angkatan_sis_id')) {
                $table->unsignedInteger('angkatan_sis_id')->nullable()->after('angkatan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_gelombang', function (Blueprint $table) {
            if (Schema::hasColumn('master_gelombang', 'angkatan_sis_id')) {
                $table->dropColumn('angkatan_sis_id');
            }
            if (Schema::hasColumn('master_gelombang', 'angkatan_id')) {
                $table->dropColumn('angkatan_id');
            }
        });
    }
};
