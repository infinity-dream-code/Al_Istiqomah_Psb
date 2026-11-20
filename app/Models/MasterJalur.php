<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJalur extends Model
{
    use HasFactory;

    protected $table = 'master_jalur';

    protected $fillable = ['nama', 'is_active', 'is_free'];

    protected $casts = ['is_active' => 'boolean', 'is_free' => 'boolean'];

    public function pembayaranSekolah()
    {
        return $this->hasMany(PembayaranSekolah::class, 'id_jalur');
    }

    public function peserta()
    {
        return $this->hasMany(DataPeserta::class, 'id_jalur');
    }
}
