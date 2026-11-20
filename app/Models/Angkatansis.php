<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tahun angkatan di SIKEU (DB 56). Tabel: angkatansis.
 * Dipakai sebagai relasi master_gelombang.angkatan_sis_id.
 */
class Angkatansis extends Model
{
    protected $connection = 'sikeu';

    protected $table = 'angkatansis';

    protected $fillable = [
        'nama_angkatan',
        'tahun',
        'keterangan',
    ];
}
