<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentSiswaMapping extends Model
{
    protected $table = 'parent_siswa_mapping';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'siswa_id',
        'is_primary',
        'created_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(ParentAccount::class, 'parent_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
