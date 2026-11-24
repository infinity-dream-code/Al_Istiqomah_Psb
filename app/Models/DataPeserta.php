<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DataPeserta extends Authenticatable
{
    use HasFactory;

    protected $table = 'data_peserta';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user',
        'no_pendaftaran',
        'id_gelombang',
        'gelombang',
        'id_jalur',
        'jalur',
        'id_fakultas',
        'fakultas',
        'id_prodi',
        'prodi',
        'id_master_harga',
        'nama_peserta',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'nisn',
        'no_akta_lahir',
        'gender',
        'agama',
        'kewarganegaraan',
        'email',
        'no_hp',
        'password',
        'plain_password',
        'kontak_utama',
        'alamat_lengkap',
        'alamat_sekolah',
        'dusun',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'kode_pos',
        'nama_sekolah',
        'kota_sekolah',
        'provinsi_sekolah',
        'id_provinsi_sekolah',
        'id_kabupaten_sekolah',
        'jurusan',
        'id_jurusan',
        'tahun_lulus',
        'status_sekolah',
        'ibu_nama',
        'ibu_tanggal_lahir',
        'ibu_nik',
        'ibu_alamat',
        'ibu_suku',
        'ibu_pendidikan',
        'id_pendidikan_ibu',
        'ibu_pekerjaan',
        'id_pekerjaan_ibu',
        'ibu_penghasilan',
        'id_penghasilan_ibu',
        'ibu_no_tlp',
        'ayah_nama',
        'ayah_tanggal_lahir',
        'ayah_nik',
        'ayah_alamat',
        'ayah_suku',
        'ayah_pendidikan',
        'id_pendidikan_ayah',
        'ayah_pekerjaan',
        'id_pekerjaan_ayah',
        'ayah_penghasilan',
        'id_penghasilan_ayah',
        'ayah_no_tlp',
        'jml_saudara_kandung',
        'jml_saudara_yayasan',
        'id_sumber',
        'nama_sumber',
        'foto',
        'no_kk',
        'dokumen_kk',
        'dokumen_ktp_ortu',
        'dokumen_akte_kelahiran',
        'va_number',
        'status_paid',
        'status_pembayaran_registrasi',
        'tgl_bayar_regis',
        'tgl_bayar_daftar',
        'status_ujian',
        'batas_awal_registrasi',
        'batas_akhir_registrasi',
        'pembekalan',
    ];

    protected $hidden = [
        'password',
        'plain_password',
    ];

    protected $appends = ['username', 'nama', 'role'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'ibu_tanggal_lahir' => 'date',
        'ayah_tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jml_saudara_kandung' => 'integer',
        'jml_saudara_yayasan' => 'integer',
        'tgl_bayar_regis' => 'date',
        'tgl_bayar_daftar' => 'date',
        'batas_awal_registrasi' => 'datetime',
        'batas_akhir_registrasi' => 'datetime',
        'pembekalan' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? '';
    }

    public function getUsernameAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_peserta'] ?? null;
    }

    public function getRoleAttribute(): string
    {
        return 'peserta';
    }

    /**
     * Kompat view/controller lama: auth()->user()->peserta
     * (user login sudah DataPeserta sendiri).
     */
    public function peserta()
    {
        return $this->hasOne(self::class, 'id', 'id');
    }

    public static function generateNoPendaftaran($idGelombang)
    {
        $gel = MasterGelombang::find($idGelombang);
        $tahun = substr($gel->tahun, -2);
        $gelombang = $gel->gelombang;
        $gelombangCode = $gelombang - 1;
        $prefix = '9' . $tahun . $gelombangCode;

        $last = static::where('no_pendaftaran', 'like', $prefix . '%')
            ->orderBy('no_pendaftaran', 'desc')
            ->value('no_pendaftaran');

        $increment = $last ? intval(substr($last, strlen($prefix))) + 1 : 1;
        $incrementStr = str_pad($increment, 6, '0', STR_PAD_LEFT);

        return $prefix . $incrementStr;
    }

    public function relasiJalur()
    {
        return $this->belongsTo(MasterJalur::class, 'id_jalur');
    }

    public function relasiProdi()
    {
        return $this->belongsTo(MasterProdi::class, 'id_prodi');
    }

    public function relasiFakultas()
    {
        return $this->belongsTo(MasterFakultas::class, 'id_fakultas');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_peserta');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'id_peserta');
    }

    public function jurusanSekolah()
    {
        return $this->belongsTo(MasterJurusanSekolah::class, 'id_jurusan');
    }

    public function user()
    {
        return $this->belongsTo(ParentAccount::class, 'id_user');
    }

    public function parentAccount()
    {
        return $this->belongsTo(ParentAccount::class, 'id_user');
    }

    public function sumberInformasi()
    {
        return $this->belongsTo(MasterSumberInformasi::class, 'id_sumber');
    }

    public function relasiGelombang()
    {
        return $this->belongsTo(MasterGelombang::class, 'id_gelombang');
    }

    public function gelombang()
    {
        return $this->belongsTo(MasterGelombang::class, 'id_gelombang');
    }

    public function jalur()
    {
        return $this->belongsTo(MasterJalur::class, 'id_jalur');
    }

    public function prodi()
    {
        return $this->belongsTo(MasterProdi::class, 'id_prodi');
    }

    public function sekolah()
    {
        return $this->hasOne(DataSekolah::class, 'id_peserta');
    }

    public function ayah()
    {
        return $this->hasOne(DataAyah::class, 'id_peserta');
    }

    public function ibu()
    {
        return $this->hasOne(DataIbu::class, 'id_peserta');
    }

    public function bantuan()
    {
        return $this->hasOne(DataBantuan::class, 'id_peserta');
    }

    public function masterHarga()
    {
        return $this->belongsTo(MasterHarga::class, 'id_master_harga');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'peserta_id');
    }
}
