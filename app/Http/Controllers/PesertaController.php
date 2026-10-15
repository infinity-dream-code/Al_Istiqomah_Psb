<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeserta;
use App\Models\MasterAgama;
use App\Models\MasterHarga;
use App\Models\MasterNegara;
use App\Models\MasterSumberInformasi;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterJurusanSekolah;
use App\Models\MasterPendidikanOrtu;
use App\Models\MasterPekerjaanOrtu;
use App\Models\MasterPenghasilanOrtu;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Rules\SafeImage;
use App\Support\SafeImageUpload;

class PesertaController extends Controller
{
    public const SUMBER_INFORMASI = ['Keluarga', 'Teman', 'Media Sosial', 'Media Cetak'];

public function index()
{
    $peserta = auth()->user()->peserta()->with(['relasiGelombang', 'ujian'])->first();

    return view('peserta.index', compact('peserta'));
}

public function lengkapi_data()
{
    $peserta = auth()->user()->peserta ?? new DataPeserta();
    $peserta->load(['bantuan','masterHarga']); 

    $negaraList = MasterNegara::all();
    $sumberInformasiList = self::SUMBER_INFORMASI;
    $provinsiList = MasterProvinsi::all();
    $pendidikanList = MasterPendidikanOrtu::all();
    $pekerjaanList = MasterPekerjaanOrtu::all();
    $penghasilanList = MasterPenghasilanOrtu::all();

    $kabupatenList = [];
    $kecamatanList = [];

    if ($peserta->id_provinsi) {
        $kabupatenList = MasterKabupaten::where('pmb_ref_provinsi_id', $peserta->id_provinsi)->get();
    }

    if ($peserta->id_kabupaten) {
        $kecamatanList = MasterKecamatan::where('pmb_ref_kota_id', $peserta->id_kabupaten)->get();
    }

    return view('peserta.lengkapi_data', compact(
        'peserta',
        'negaraList',
        'sumberInformasiList',
        'provinsiList',
        'kabupatenList',
        'kecamatanList',
        'pendidikanList',
        'pekerjaanList',
        'penghasilanList'
    ));
}


public function simpan_bantuan(Request $request)
{
    $request->validate([
        'no_kks' => 'nullable|string|max:20',
        'no_kps' => 'nullable|string|max:20',
        'usulan_pip' => 'nullable|string',
        'nomor_kip' => 'nullable',
        'nama_kip' => 'nullable|string|max:100',
        'alasan_menolak_kip' => 'nullable|string',
        'no_reg_akta_lahir' => 'nullable',
    ]);

    $user = auth()->user();
    $peserta = $user->peserta;

    if (!$peserta) {
        return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
    }

    $peserta->bantuan()->updateOrCreate(
        ['id_peserta' => $peserta->id],
        [
            'no_kks' => $request->no_kks,
            'no_kps' => $request->no_kps,
            'usulan_pip' => $request->usulan_pip,
            'nomor_kip' => $request->nomor_kip,
            'nama_kip' => $request->nama_kip,
            'alasan_menolak_kip' => $request->alasan_menolak_kip,
            'no_reg_akta_lahir' => $request->no_reg_akta_lahir,
        ]
    );

    return redirect()->back()->with('success', 'Data bantuan berhasil disimpan!');
}

public function simpan_data(Request $request)
{
    $request->validate([
        'nik' => 'required|digits:16',
        'no_akta_lahir' => 'required',
        'no_kk' => 'required|digits:16',
        'ibu_nik' => 'nullable|digits:16',
        'ayah_nik' => 'nullable|digits:16',
        'nisn' => 'nullable|digits:10',
        'tahun_lulus' => 'nullable|digits:4',
        'nama_sumber' => ['nullable', \Illuminate\Validation\Rule::in(self::SUMBER_INFORMASI)],
        'foto' => ['nullable', 'file', new SafeImage()],
        'dokumen_kk' => ['nullable', 'file', new SafeImage()],
        'dokumen_akte_kelahiran' => ['nullable', 'file', new SafeImage()],
    ], [], [
        'foto' => 'Foto',
        'dokumen_kk' => 'Kartu Keluarga',
        'dokumen_akte_kelahiran' => 'Akta Kelahiran',
        'nama_sumber' => 'Dapat Informasi Dari',
    ]);

    $user = auth()->user();
    $peserta = $user->peserta ?? new DataPeserta();
    $peserta->id_user = $user->id;

    $fillable = [
        'nama_peserta', 'nik', 'tempat_lahir', 'tanggal_lahir',
        'nisn', 'no_akta_lahir', 'no_kk',
        'kewarganegaraan', 'email', 'no_hp', 'alamat_lengkap',
        'dusun', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'kode_pos',
        'nama_sekolah', 'tahun_lulus',
        'ibu_nama', 'ibu_tanggal_lahir', 'ibu_nik',
        'id_pendidikan_ibu', 'id_pekerjaan_ibu', 'id_penghasilan_ibu', 'ibu_no_tlp',
        'ayah_nama', 'ayah_tanggal_lahir', 'ayah_nik',
        'id_pendidikan_ayah', 'id_pekerjaan_ayah', 'id_penghasilan_ayah', 'ayah_no_tlp',
        'jml_saudara_kandung', 'jml_saudara_yayasan', 'nama_sumber'
    ];

    foreach ($fillable as $field) {
        $peserta->$field = $request->$field ?? $peserta->$field;
    }

    if ($request->gender) {
        $peserta->gender = $request->gender === 'Laki-laki' ? 'L' : 'P';
    }

    if ($request->filled('id_pendidikan_ibu')) {
        $peserta->ibu_pendidikan = \App\Models\MasterPendidikanOrtu::find($request->id_pendidikan_ibu)->nama ?? null;
    }
    if ($request->filled('id_pendidikan_ayah')) {
        $peserta->ayah_pendidikan = \App\Models\MasterPendidikanOrtu::find($request->id_pendidikan_ayah)->nama ?? null;
    }
    if ($request->filled('nama_sumber')) {
        $peserta->id_sumber = MasterSumberInformasi::where('nama', $request->nama_sumber)->value('id');
    }

    $tahun = $peserta->relasiGelombang->tahun ?? date('Y');

    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');
        $img = (new ImageManager(new Driver()))->read($foto->getRealPath());
        if (round($img->width() / $img->height(), 2) != round(3 / 4, 2)) {
            return back()->withErrors(['foto' => 'Foto harus berukuran 3x4 (rasio 3:4)'])->withInput();
        }
        $this->replaceUpload($peserta, 'foto', $foto, "uploads/foto/foto-$tahun");
    }

    $dokumen = [
        'dokumen_kk' => "uploads/dokumen/kk-$tahun",
        'dokumen_akte_kelahiran' => "uploads/dokumen/akta-$tahun",
    ];
    foreach ($dokumen as $field => $directory) {
        if ($request->hasFile($field)) {
            $this->replaceUpload($peserta, $field, $request->file($field), $directory);
        }
    }

    $peserta->save();

    return redirect('PmbMstPendaftarans/lengkapi_data')->with('success', 'Data berhasil disimpan!');
}

private function replaceUpload(DataPeserta $peserta, string $field, \Illuminate\Http\UploadedFile $file, string $directory): void
{
    $old = $peserta->$field;
    $peserta->$field = SafeImageUpload::store($file, $directory);

    if ($old && $old !== $peserta->$field && Storage::disk('public')->exists($old)) {
        Storage::disk('public')->delete($old);
    }
}

}
