<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nama tagihan_daful agar tidak bentrok dengan SIKEU.tagihan di DB 56.
        Schema::create('tagihan_daful', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_peserta')->nullable();
            $table->decimal('biaya_daful', 15, 2)->nullable();
            $table->json('detail')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->dateTime('tanggal_pembayaran_daful')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_daful');
    }
};
