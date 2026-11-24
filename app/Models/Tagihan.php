<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    /** SPMB daftar-ulang (bukan SIKEU.tagihan keuangan). */
    protected $table = 'tagihan_daful';

    protected $fillable = [
        'id_peserta',
        'biaya_daful',
        'detail',
        'status',
        'tanggal_pembayaran_daful',
    ];

    protected $casts = [
        'detail' => 'array',
        'status' => 'integer',
        'tanggal_pembayaran_daful' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(DataPeserta::class, 'id_peserta');
    }
}
