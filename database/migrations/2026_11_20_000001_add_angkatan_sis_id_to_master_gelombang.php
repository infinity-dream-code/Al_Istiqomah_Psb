<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_gelombang', function (Blueprint $table) {
            if (!Schema::hasColumn('master_gelombang', 'angkatan_sis_id')) {
                // Logical FK ke SIKEU.angkatansis (tahun angkatan). Physical FK cross-DB tidak dipakai.
                $table->unsignedInteger('angkatan_sis_id')->nullable()->after('tahun_akademik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_gelombang', function (Blueprint $table) {
            if (Schema::hasColumn('master_gelombang', 'angkatan_sis_id')) {
                $table->dropColumn('angkatan_sis_id');
            }
        });
    }
};
