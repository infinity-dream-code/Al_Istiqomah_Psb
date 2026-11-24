<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** SIKEU DB 56 — tabel `angkatansis` (= tahun angkatan). */
class SikeuAngkatansis extends Model
{
    protected $connection = 'sikeu';

    protected $table = 'angkatansis';

    protected $fillable = ['nama_angkatan', 'tahun', 'keterangan'];
}
