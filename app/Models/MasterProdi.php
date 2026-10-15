<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProdi extends Model
{
    use HasFactory;

    protected $table = 'master_prodi';

    protected $fillable = ['id_fakultas','nama'];

    // Jurusan disembunyikan dari UI, tapi master_harga & data_peserta tetap butuh id_prodi.
    public const DEFAULT_NAMA = '-';

    public static function defaultFor(int $idFakultas): self
    {
        return static::firstOrCreate(['id_fakultas' => $idFakultas, 'nama' => self::DEFAULT_NAMA]);
    }

    public static function resolveFor(int $idFakultas, $idProdi = null): self
    {
        if ($idProdi) {
            $prodi = static::where('id', $idProdi)->where('id_fakultas', $idFakultas)->first();
            if ($prodi) {
                return $prodi;
            }
        }

        return static::defaultFor($idFakultas);
    }

     public function targets()
    {
        return $this->hasMany(Target::class, 'id_prodi');
    }

     public function latestTarget()
    {
        return $this->hasOne(Target::class, 'id_prodi', 'id')->latestOfMany();
    }

    public function fakultas()
    {
        return $this->belongsTo(MasterFakultas::class,'id_fakultas');
    }

    public function pembayaranSekolah()
    {
        return $this->hasMany(PembayaranSekolah::class,'id_prodi');
    }


    public function peserta()
    {
        return $this->hasMany(DataPeserta::class,'id_prodi');
    }

    
}
