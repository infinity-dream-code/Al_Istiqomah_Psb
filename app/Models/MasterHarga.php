<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterHarga extends Model
{
    protected $table = 'master_harga';

    protected $fillable = [
        'id_jalur',
        'id_gelombang',
        'id_fakultas',
        'id_prodi',
        'harga_final',
        'harga_registrasi',
        'harga',
        'detail',
        'detail_pendaftaran',
        'detail_registrasi',
        'nama_jalur',
        'nama_gelombang',
        'nama_fakultas',
        'nama_prodi',
        'active',
        'status',
        'kode',
        'nama',
        'unit_id',
        'tahun_ajaran_id',
        'jenis_siswa_id',
        'akun_penerimaan_id',
        'akun_registrasi_id',
    ];

    protected $casts = [
        'detail' => 'array',
        'detail_pendaftaran' => 'array',
        'detail_registrasi' => 'array',
        'harga_final' => 'decimal:2',
        'harga_registrasi' => 'decimal:2',
        'harga' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function jalur()
    {
        return $this->belongsTo(MasterJalur::class, 'id_jalur');
    }

    public function gelombang()
    {
        return $this->belongsTo(MasterGelombang::class, 'id_gelombang');
    }

    public function fakultas()
    {
        return $this->belongsTo(MasterFakultas::class, 'id_fakultas');
    }

    public function prodi()
    {
        return $this->belongsTo(MasterProdi::class, 'id_prodi');
    }
}
