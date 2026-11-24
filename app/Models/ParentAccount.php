<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Akun login pendaftar/ortu (SIKEU.parents) — pengganti `user` untuk peserta SPMB.
 */
class ParentAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'parents';

    protected $fillable = [
        'no_wa',
        'password',
        'plain_password',
        'nama_lengkap',
        'email',
        'alamat',
        'status',
    ];

    protected $hidden = [
        'password',
        'plain_password',
    ];

    protected $appends = ['username', 'nama', 'role'];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getUsernameAttribute(): ?string
    {
        return $this->attributes['no_wa'] ?? null;
    }

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_lengkap'] ?? null;
    }

    public function getRoleAttribute(): string
    {
        return 'peserta';
    }

    public function peserta()
    {
        return $this->hasOne(DataPeserta::class, 'id_user');
    }

    public function mappings()
    {
        return $this->hasMany(ParentSiswaMapping::class, 'parent_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'parent_siswa_mapping', 'parent_id', 'siswa_id')
            ->withPivot(['is_primary', 'created_at']);
    }
}
