<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGelombang extends Model
{
    use HasFactory;

    protected $table = 'master_gelombang';

    protected $fillable = [
        'tahun',
        'gelombang',
        'tahun_akademik',
        'start',
        'end',
        'pengumuman',
        'is_active'
    ];

    protected $casts = [
        'start' => 'date',
        'end' => 'date',
        'pengumuman' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function pembayaranSekolah()
    {
        return $this->hasMany(PembayaranSekolah::class, 'id_gelombang');
    }

    public function peserta()
    {
        return $this->hasMany(DataPeserta::class, 'id_gelombang');
    }

    public function harga()
    {
        return $this->hasMany(MasterHarga::class, 'id_gelombang');
    }

    public function akademik()
{
    return $this->belongsTo(MasterAkademik::class, 'tahun_akademik_id');
}

}
