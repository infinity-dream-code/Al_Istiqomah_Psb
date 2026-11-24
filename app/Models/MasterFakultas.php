<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Sekolah SPMB = SIKEU.unit_sekolah (DB 56).
 */
class MasterFakultas extends Model
{
    use HasFactory;

    protected $table = 'unit_sekolah';

    protected $fillable = ['fakultas', 'aktif', 'kode_unit', 'nama_unit', 'alamat', 'no_telp', 'email', 'status'];

    protected $appends = ['fakultas', 'aktif'];

    public function prodi()
    {
        return $this->hasMany(MasterProdi::class, 'id_fakultas');
    }

    public function getFakultasAttribute(): ?string
    {
        return $this->attributes['nama_unit'] ?? null;
    }

    public function setFakultasAttribute($value): void
    {
        $this->attributes['nama_unit'] = $value;
    }

    public function getAktifAttribute(): bool
    {
        return ($this->attributes['status'] ?? '') === 'active';
    }

    public function setAktifAttribute($value): void
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'active' : 'inactive';
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->attributes['kode_unit'])) {
                $nama = $model->attributes['nama_unit'] ?? 'UNIT';
                $model->attributes['kode_unit'] = strtoupper(substr(preg_replace('/\s+/', '', $nama), 0, 10));
            }
            if (empty($model->attributes['status'])) {
                $model->attributes['status'] = 'active';
            }
            if (empty($model->attributes['nama_unit'])) {
                $model->attributes['nama_unit'] = 'UNIT';
            }
        });
    }
}
