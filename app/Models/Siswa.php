<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nomor_pendaftaran',
        'va_number',
        'is_calon_spmb',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'parent_id',
        'kelas_id',
        'jenis_siswa_id',
        'angkatan_id',
        'angkatan_sis_id',
        'unit_id',
        'tahun_ajaran_id',
        'status',
    ];

    protected $casts = [
        'is_calon_spmb' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function parent()
    {
        return $this->belongsTo(ParentAccount::class, 'parent_id');
    }
}
