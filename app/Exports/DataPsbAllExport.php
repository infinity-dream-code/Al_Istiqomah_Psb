<?php

namespace App\Exports;

use App\Models\DataPeserta;
use App\Models\MasterGelombang;
use App\Models\MasterHarga;
use App\Models\DataBantuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataPsbAllExport implements FromCollection, WithHeadings
{
    protected $tahun;
    protected $idGelombang;
    protected $search;

    public function __construct($tahun = null, $idGelombang = null, $search = null)
    {
        $this->tahun = $tahun;
        $this->idGelombang = $idGelombang;
        $this->search = $search;
    }

    public function collection()
    {
        $pesertaQuery = DataPeserta::with(['masterHarga', 'user']);

        if ($this->tahun) {
            $idsGelByTahun = MasterGelombang::where('tahun_akademik', $this->tahun)->pluck('id');
            $pesertaQuery->whereIn('id_gelombang', $idsGelByTahun);
        }

        if ($this->idGelombang) {
            $pesertaQuery->where('id_gelombang', $this->idGelombang);
        }

        if ($this->search) {
            $pesertaQuery->where(function ($q) {
                $q->where('nama_peserta', 'like', "%{$this->search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$this->search}%")
                  ->orWhere('jalur', 'like', "%{$this->search}%")
                  ->orWhere('fakultas', 'like', "%{$this->search}%")
                  ->orWhere('prodi', 'like', "%{$this->search}%")
                  ->orWhere('gelombang', 'like', "%{$this->search}%");
            });
        }

        $peserta = $pesertaQuery->get();

        return $peserta->map(function ($p) {
            $masterHarga = $p->id_master_harga ? MasterHarga::find($p->id_master_harga) : null;
            $bantuan = DataBantuan::where('id_peserta', $p->id)->first();

            $verifikasi = ($p->foto && $p->dokumen_kk && $p->dokumen_akte_kelahiran) ? 'Lengkap' : 'Belum Lengkap';
            $bayarRegistrasi = $p->status_pembayaran_registrasi == 1 ? 'Lunas' : 'Belum Lunas';

            return [
                'Created' => $p->created_at ? $p->created_at->format('Y-m-d') : '-',
                'Nama' => $p->nama_peserta ?? '-',
                'No Pendaftaran' => $p->no_pendaftaran ?? '-',
                'Virtual Akun' => $p->va_number ?? '-',
                'NIS' => $p->nis ?? '-',
                'NISN' => $p->nisn ?? '-',
                'Verifikasi Dokumen' => $verifikasi,
                'Download Kartu Ujian' => $p->status_ujian ? 'Sudah' : 'Belum',
                'Status Ujian' => $p->status_ujian ?? '-',
                'Status Lulus' => $p->status_lulus ?? '-',
                'Jalur' => $p->jalur ?? '-',
                'Gelombang' => $p->gelombang ?? '-',
                'Daftar Pada Tahun Akademik' => $p->tahun_akademik ?? '-',
                'Untuk Ajaran' => $p->tahun ?? '-',
                'Jurusan' => $p->prodi ?? '-',
                'Sekolah' => $p->fakultas ?? '-',
                'Email' => $p->email ?? '-',
                'No Hp Siswa' => $p->no_hp ?? '-',
                'Lunas Pendaftaran' => $p->status_paid == 1 ? 'Lunas' : 'Belum Lunas',
                'ID' => $p->id ?? '-',
                'Jenis Kelamin' => $p->gender ?? '-',
                'Warga Negara' => $p->kewarganegaraan ?? '-',
                'Tempat Lahir' => $p->tempat_lahir ?? '-',
                'Tanggal Lahir' => $p->tanggal_lahir ?? '-',
                'Agama' => $p->agama ?? '-',
                'NIK' => $p->nik ?? '-',
                'No. KK' => $p->no_kk ?? '-',
                'No. Akte Lahir' => $p->no_akta_lahir ?? '-',
                'Alamat' => $p->alamat_lengkap ?? '-',
                'Dusun Rumah' => $p->dusun ?? '-',
                'Kecamatan Rumah' => $p->kecamatan ?? '-',
                'Provinsi Rumah' => $p->provinsi ?? '-',
                'Kota Rumah' => $p->kabupaten ?? '-',
                'Kodepos' => $p->kode_pos ?? '-',
                'Jenis Tinggal' => $p->status_sekolah ?? '-',
                'Nama Ibu' => $p->ibu_nama ?? '-',
                'Tanggal Lahir Ibu' => $p->ibu_tanggal_lahir ?? '-',
                'Suku Ibu' => $p->ibu_suku ?? '-',
                'NIK Ibu' => $p->ibu_nik ?? '-',
                'Alamat Ibu' => $p->ibu_alamat ?? '-',
                'Telepon Ibu' => $p->ibu_no_tlp ?? '-',
                'Penghasilan Ibu' => $p->ibu_penghasilan ?? '-',
                'Pendidikan Ibu' => $p->ibu_pendidikan ?? '-',
                'Pekerjaan Ibu' => $p->ibu_pekerjaan ?? '-',
                'Nama Ayah' => $p->ayah_nama ?? '-',
                'Tanggal Lahir Ayah' => $p->ayah_tanggal_lahir ?? '-',
                'Suku Ayah' => $p->ayah_suku ?? '-',
                'NIK Ayah' => $p->ayah_nik ?? '-',
                'Alamat Ayah' => $p->ayah_alamat ?? '-',
                'Telepon Ayah' => $p->ayah_no_tlp ?? '-',
                'Penghasilan Ayah' => $p->ayah_penghasilan ?? '-',
                'Pendidikan Ayah' => $p->ayah_pendidikan ?? '-',
                'Pekerjaan Ayah' => $p->ayah_pekerjaan ?? '-',
                'Kota Sekolah' => $p->kota_sekolah ?? '-',
                'Provinsi Sekolah' => $p->provinsi_sekolah ?? '-',
                'Nama Sekolah' => $p->nama_sekolah ?? '-',
                'Tahun Lulus' => $p->tahun_lulus ?? '-',
                'No Ijazah' => $p->no_ijazah ?? '-',
                'No SKHUN' => $p->no_skhun ?? '-',
                'No Ujian Nas.' => $p->no_un ?? '-',
                'No Kartu Kel. Sejahtera' => $bantuan->no_kks ?? '-',
                'No Kartu Perlindungan Nas.' => $bantuan->no_kps ?? '-',
                'Alasan Sekolah Layak PIP' => $bantuan->usulan_pip ?? '-',
                'No. KIP' => $bantuan->nomor_kip ?? '-',
                'Nama Pada KIP' => $bantuan->nama_kip ?? '-',
                'Als. Menolak KIP' => $bantuan->alasan_menolak_kip ?? '-',
                'No. Reg Akta Lahir' => $bantuan->no_reg_akta_lahir ?? '-',
                'Jumlah Saudara Kandung' => $p->jml_saudara_kandung ?? '-',
                'Saudara Kandung Sekolah di YYS' => $p->jml_saudara_yayasan ?? '-',
                'Kata Kunci' => $p->nama_sumber ?? '-',
                'Bayar Registrasi' => $p->status_paid == 1 ? 'Lunas' : 'Belum Lunas',
                'Bayar Registrasi Daftar Ulang' => $bayarRegistrasi,
                'Nominal Registrasi Daftar Ulang' => $masterHarga ? $masterHarga->harga_registrasi : '-',
                'Detail Biaya Daftar Ulang' => $masterHarga ? json_encode($masterHarga->detail) : '-',
                'Password Login' => $p->user->plain_password ?? '-',

            ];
        });
    }

    public function headings(): array
    {
        return [
            'Created','Nama','No Pendaftaran','Virtual Akun','NIS','NISN','Verifikasi Dokumen','Download Kartu Ujian',
            'Status Ujian','Status Lulus','Jalur','Gelombang','Daftar Pada Tahun Akademik','Untuk Ajaran','Program','Unit',
            'Email','No Hp Siswa','Lunas Pendaftaran','ID','Jenis Kelamin','Warga Negara','Tempat Lahir','Tanggal Lahir','Agama',
            'NIK','No. KK','No. Akte Lahir','Alamat','Dusun Rumah','Kecamatan Rumah','Provinsi Rumah','Kota Rumah','Kodepos',
            'Jenis Tinggal','Nama Ibu','Tanggal Lahir Ibu','Suku Ibu','NIK Ibu','Alamat Ibu','Telepon Ibu','Penghasilan Ibu',
            'Pendidikan Ibu','Pekerjaan Ibu','Nama Ayah','Tanggal Lahir Ayah','Suku Ayah','NIK Ayah','Alamat Ayah','Telepon Ayah',
            'Penghasilan Ayah','Pendidikan Ayah','Pekerjaan Ayah','Kota Sekolah','Provinsi Sekolah',
            'Nama Sekolah','Tahun Lulus','No Ijazah','No SKHUN','No Ujian Nas.','No Kartu Kel. Sejahtera',
            'No Kartu Perlindungan Nas.','Alasan Sekolah Layak PIP','No. KIP','Nama Pada KIP','Als. Menolak KIP',
            'No. Reg Akta Lahir','Jumlah Saudara Kandung','Saudara Kandung Sekolah di YYS','Kata Kunci',
            'Bayar Registrasi','Bayar Registrasi Daftar Ulang','Nominal Registrasi Daftar Ulang','Detail Biaya Registrasi Daftar Ulang','Password Login'
        ];
    }
}