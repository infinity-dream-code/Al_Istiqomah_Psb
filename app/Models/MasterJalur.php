<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Jalur SPMB = SIKEU.jenis_siswa (DB 56).
 */
class MasterJalur extends Model
{
    use HasFactory;

    protected $table = 'jenis_siswa';

    protected $fillable = ['nama', 'is_active', 'is_free', 'kode_jenis', 'nama_jenis', 'status', 'keterangan', 'unit_id'];

    protected $appends = ['nama', 'is_active', 'is_free'];

    public function pembayaranSekolah()
    {
        return $this->hasMany(PembayaranSekolah::class, 'id_jalur');
    }

    public function peserta()
    {
        return $this->hasMany(DataPeserta::class, 'id_jalur');
    }

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_jenis'] ?? null;
    }

    public function setNamaAttribute($value): void
    {
        $this->attributes['nama_jenis'] = $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return ($this->attributes['status'] ?? '') === 'active';
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'active' : 'inactive';
    }

    public function getIsFreeAttribute(): bool
    {
        return false;
    }

    public function setIsFreeAttribute($value): void
    {
        // jenis_siswa tidak punya is_free — diabaikan.
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->attributes['kode_jenis'])) {
                $nama = $model->attributes['nama_jenis'] ?? 'JNS';
                $model->attributes['kode_jenis'] = strtoupper(substr(preg_replace('/\s+/', '', $nama), 0, 10)) . random_int(10, 99);
            }
            if (empty($model->attributes['status'])) {
                $model->attributes['status'] = 'active';
            }
            if (empty($model->attributes['nama_jenis'])) {
                $model->attributes['nama_jenis'] = 'Reguler';
            }
        });
    }
}
