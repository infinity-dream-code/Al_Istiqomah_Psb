<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** SIKEU DB 56 — tabel `angkatan` (= gelombang). */
class SikeuAngkatan extends Model
{
    protected $connection = 'sikeu';

    protected $table = 'angkatan';

    protected $fillable = ['nama_angkatan', 'tahun', 'keterangan'];
}
