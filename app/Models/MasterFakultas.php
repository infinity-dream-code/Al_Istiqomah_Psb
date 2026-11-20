<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterFakultas extends Model
{
    use HasFactory;

    protected $table = 'master_fakultas';

    protected $fillable = ['fakultas','aktif'];

    protected $casts = [
        'aktif' => 'boolean'
    ];

     public function prodi()
    {
        return $this->hasMany(MasterProdi::class,'id_fakultas');
    }
    
}
