<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterJalur;
use App\Models\MasterGelombang;
use App\Models\MasterFakultas;
use App\Models\MasterProdi;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Exports\PesertaRegistrasiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterKecamatan;
use App\Models\MasterHarga;
use App\Models\MasterJurusanSekolah;
use App\Models\MasterPenghasilanOrtu;
use App\Models\MasterPekerjaanOrtu;
use App\Models\MasterSumberInformasi;
use App\Models\DataPeserta;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RegisterController extends Controller
{


  public function enroll(Request $request)
{
    $request->validate([
        'gender'            => 'required|in:L,P',
        'nama'              => 'required|string|max:100',
        'id_gelombang'      => 'required|integer',
        'id_jalur'          => 'required|integer',
        'id_fakultas'       => 'required|integer',
        'asal_sekolah'      => 'required|string|max:150',
        'tahun_lulus'       => 'required|digits:4',
    ]);

    // Jurusan tidak dipilih di form: harga diambil dari jalur + gelombang + sekolah.
    $masterHarga = MasterHarga::where('id_fakultas', $request->id_fakultas)
        ->where('id_gelombang', $request->id_gelombang)
        ->where('id_jalur', $request->id_jalur)
        ->orderByDesc('active')
        ->orderBy('id')
        ->first();

    if (!$masterHarga) {
        return back()
            ->withInput()
            ->withErrors(['id_fakultas' => 'Biaya pendaftaran untuk jalur, gelombang, dan sekolah yang dipilih belum diatur. Silakan hubungi panitia.']);
    }

   $noDaftar = DataPeserta::generateNoPendaftaran($request->id_gelombang);
    $password = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

    $user = User::create([
        'nama'           => $request->nama,
        'username'       => $noDaftar,
        'password'       => Hash::make($password),
        'plain_password' => $password,
        'role'           => 'peserta',
    ]);

    $vaNumber = config('client.va_prefix') . $noDaftar;

    $gelombang = MasterGelombang::find($request->id_gelombang);
    $jalur     = MasterJalur::find($request->id_jalur);
    $fakultas  = MasterFakultas::find($request->id_fakultas);

    $provinsi       = MasterProvinsi::find($request->provinsi_id);
    $kabupaten      = MasterKabupaten::find($request->kabupaten_id);
    $kecamatan      = MasterKecamatan::find($request->kecamatan_id);

    $pekerjaanIbu    = MasterPekerjaanOrtu::find($request->pekerjaan_ibu);
    $penghasilanIbu  = MasterPenghasilanOrtu::find($request->penghasilan_ibu);
    $pekerjaanAyah   = MasterPekerjaanOrtu::find($request->pekerjaan_ayah);
    $penghasilanAyah = MasterPenghasilanOrtu::find($request->penghasilan_ayah);

    $peserta = DataPeserta::create([
        'id_user'          => $user->id,
        'no_pendaftaran'   => $noDaftar,
        'nama_peserta'     => $request->nama,
        'tempat_lahir'     => $request->tempat_lahir,
        'tanggal_lahir'    => $request->tanggal_lahir,
        'id_gelombang'     => $request->id_gelombang,
        'gelombang'        => $gelombang?->gelombang,
        'id_jalur'         => $request->id_jalur,
        'jalur'            => $jalur?->nama,
        'id_fakultas'      => $request->id_fakultas,
        'fakultas'         => $fakultas?->fakultas,
        'id_prodi'         => $masterHarga->id_prodi,
        'prodi'            => '-',
        'id_master_harga'  => $masterHarga->id,
        'gender'           => $request->gender,
        'status_paid'      => 0,
        'va_number'        => $vaNumber,
        'dusun'            => $request->dusun,
        'id_provinsi'      => $request->provinsi_id,
        'provinsi'         => $provinsi?->Provinsi,
        'id_kabupaten'     => $request->kabupaten_id,
        'kabupaten'        => $kabupaten?->kota,
        'id_kecamatan'     => $request->kecamatan_id,
        'kecamatan'        => $kecamatan?->kecamatan,
        'nama_sekolah'     => $request->asal_sekolah,
        'tahun_lulus'      => $request->tahun_lulus,
        'ibu_nama'         => $request->nama_ibu,
        'id_pekerjaan_ibu' => $request->pekerjaan_ibu,
        'ibu_pekerjaan'    => $pekerjaanIbu?->nama,
        'id_penghasilan_ibu'=> $request->penghasilan_ibu,
        'ibu_penghasilan'   => $penghasilanIbu?->nama,
        'ibu_no_tlp'       => $request->tlp_ibu,
        'ayah_nama'        => $request->nama_ayah,
        'id_pekerjaan_ayah'=> $request->pekerjaan_ayah,
        'ayah_pekerjaan'   => $pekerjaanAyah?->nama,
        'id_penghasilan_ayah'=> $request->penghasilan_ayah,
        'ayah_penghasilan' => $penghasilanAyah?->nama,
        'ayah_no_tlp'      => $request->tlp_ayah,
    ]);

    $jwtKey  = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";
    $payload = ["nomor_pendaftaran" => $noDaftar];
    $token   = JWT::encode($payload, $jwtKey, 'HS256');

    return redirect()->route('pmb.success', $token)->with('password_plain', $password);
}


public function success($token)
{
    $jwtKey = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";

    try {
        $decoded = JWT::decode($token, new Key($jwtKey, 'HS256'));
        $noDaftar = $decoded->nomor_pendaftaran;
    } catch (\Exception $e) {
        abort(403, 'Token tidak valid');
    }

$peserta = DataPeserta::where('no_pendaftaran', $noDaftar)
    ->with(['user', 'masterHarga', 'gelombang'])
    ->firstOrFail();

    $passwordPlain = $peserta->user->plain_password;

   $payload = [
    "siswa" => [
        "nama_siswa" => $peserta->nama_peserta,
        "nomor_pendaftaran" => $peserta->no_pendaftaran,
        "id_sekolah" => null,
        "yayasan" => null,
        "unit" =>  $peserta->fakultas,
        "kelas" => $peserta->prodi,
        "id_kelas" => null,
        "jenjang" => null,
        "tahun_akademik" => $peserta->relasiGelombang?->tahun_akademik,
        "alamat_rumah" => $peserta->alamat_lengkap ?? "-"
    ],
    "tagihan" => [
        "periode" => date('Ym'), 
        "nama_tagihan" => "TAGIHAN_PENDAFTARAN",
       "tahun_akademik" => $peserta->relasiGelombang?->tahun_akademik,
        "detail_tagihan" => [
            [
                "nama_tagihan" => "Biaya Pendaftaran",
                "kode_tagihan" => "499",
                "nominal" => $peserta->masterHarga->harga_final
            ]
        ]
    ]
];


    $jwtToken = JWT::encode($payload, $jwtKey, 'HS256');

    // Peserta sudah tersimpan; kalau WS tagihan down, halaman sukses tetap tampil dan tagihan dibuat ulang saat halaman dibuka lagi.
    $wsResponse = null;
    try {
        $response = Http::timeout(15)->connectTimeout(5)->withHeaders([
            'Content-Type' => 'application/json'
        ])->post(config('services.psb.ws_url'), [
            "token"  => $jwtToken,
            "method" => "CreateTagihan"
        ]);
        \Log::info('WS CreateTagihan raw', [$response->body()]);
        $wsResponse = $response->json();
    } catch (\Throwable $e) {
        \Log::error('WS CreateTagihan gagal', [
            'no_pendaftaran' => $peserta->no_pendaftaran,
            'error' => $e->getMessage(),
        ]);
    }

    return view('pmb.success', compact('peserta', 'passwordPlain'))->with('ws_response', $wsResponse);
}

public function cekTagihan($no_pendaftaran)
{
    $jwtKey = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";
    $payload = ["nomor_pendaftaran" => $no_pendaftaran];
    $token = JWT::encode($payload, $jwtKey, 'HS256');

   $response = Http::withHeaders([
    'Content-Type' => 'application/json'
])->post(config('services.psb.ws_url'), [
    "token"  => $token,
    "method" => "CekTagihan"
]);


    \Log::info('WS CekTagihan raw', [$response->body()]);

    return response()->json($response->json());
}

public function cekStatusRegis(Request $request)
{
    \Log::info('Cek Status Regis - Request Data', $request->all());

    $noPendaftaranList = $request->no_pendaftaran ?? [];

    if (empty($noPendaftaranList)) {
        \Log::warning('Cek Status Regis - Nomor pendaftaran kosong');
        return response()->json(['message' => 'Nomor pendaftaran tidak ditemukan'], 404);
    }

    $jwtKey = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";
    $payload = [
        "nomor_pendaftaran" => $noPendaftaranList,
        "nama_tagihan" => "TAGIHAN_PENDAFTARAN_ULANG"
    ];

    $token = \Firebase\JWT\JWT::encode($payload, $jwtKey, 'HS256');

    \Log::info('Cek Status Regis - Payload', $payload);

    $response = \Http::post(config('services.psb.ws_url'), [
        "token" => $token,
        "method" => "cekTagihanDibayar"
    ]);

    \Log::info('Cek Status Regis - Raw Response', [$response->body()]);

    if (!$response->successful()) {
        \Log::error('Cek Status Regis - WS gagal diakses', ['status' => $response->status()]);
        return response()->json(['message' => 'WS gagal diakses'], 500);
    }

    $result = $response->json();

    if (($result['status'] ?? 422) !== 200) {
        \Log::warning('Cek Status Regis - WS error', $result);
        return response()->json(['message' => $result['message'] ?? 'Gagal cek tagihan'], 422);
    }

    foreach ($result['data'] as $tagihan) {
        if ($tagihan['StatusBayar'] == "1") {
            DataPeserta::where('no_pendaftaran', $tagihan['NomorPendaftaran'])
                ->update([
                    'status_pembayaran_registrasi' => 1,
                    'tgl_bayar_regis' => $tagihan['TanggalBayar']
                ]);
            \Log::info('Cek Status Regis - Update Peserta', [
                'no_pendaftaran' => $tagihan['NomorPendaftaran'],
                'tanggal_bayar' => $tagihan['TanggalBayar']
            ]);
        }
    }

    return response()->json(['message' => 'Status registrasi berhasil dicek']);
}

public function cekStatus(Request $request)
{
    $noPendaftaranList = json_decode($request->no_pendaftaran, true) ?? [];

    if (empty($noPendaftaranList)) {
        return back()->with('error', 'Pilih minimal satu peserta');
    }

    $jwtKey = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";
    $payload = [
        "nomor_pendaftaran" => $noPendaftaranList,
        "nama_tagihan" => "TAGIHAN_PENDAFTARAN_ULANG"
    ];

    $token = \Firebase\JWT\JWT::encode($payload, $jwtKey, 'HS256');

    $response = \Http::post(config('services.psb.ws_url'), [
        "token" => $token,
        "method" => "cekTagihanDibayar"
    ]);

    if (!$response->successful()) {
        return back()->with('error', 'WS gagal diakses');
    }

    $result = $response->json();

    if (($result['status'] ?? 422) !== 200) {
        return back()->with('error', $result['message'] ?? 'Gagal cek tagihan');
    }

    $updatedCount = 0;
    foreach ($result['data'] as $tagihan) {
        if ($tagihan['StatusBayar'] == "1") {
            $peserta = \App\Models\DataPeserta::where('no_pendaftaran', $tagihan['NomorPendaftaran'])->first();

            if ($peserta) {
                $updated = $peserta->where(function ($q) {
                        $q->whereNull('status_pembayaran_registrasi')
                          ->orWhere('status_pembayaran_registrasi', 0);
                    })
                    ->update([
                        'status_pembayaran_registrasi' => 1,
                        'tgl_bayar_regis' => $tagihan['TanggalBayar']
                    ]);

                if ($updated) {
                    \DB::table('tagihan')
                        ->where('id_peserta', $peserta->id)
                        ->update([
                            'status' => 1,
                            'tanggal_pembayaran_daful' => $tagihan['TanggalBayar'],
                            'updated_at' => now()
                        ]);
                    $updatedCount++;
                }
            }
        }
    }

    if ($updatedCount > 0) {
        return back()->with('success', "Berhasil update status {$updatedCount} peserta");
    }

    return back()->with('success', 'Berhasil cek status');
}

public function cekStatus1(Request $request)
{
    $noPendaftaranList = json_decode($request->no_pendaftaran, true) ?? [];

    if (empty($noPendaftaranList)) {
        return back()->with('error', 'Pilih minimal satu peserta');
    }

    $jwtKey = "53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4";
    $payload = [
        "nomor_pendaftaran" => $noPendaftaranList,
        "nama_tagihan" => "TAGIHAN_PENDAFTARAN_ULANG"
    ];

    $token = \Firebase\JWT\JWT::encode($payload, $jwtKey, 'HS256');

    $response = \Http::post(config('services.psb.ws_url'), [
        "token" => $token,
        "method" => "cekTagihanDibayar"
    ]);

    if (!$response->successful()) {
        return back()->with('error', 'WS gagal diakses');
    }

    $result = $response->json();

    if (($result['status'] ?? 422) !== 200) {
        return back()->with('error', $result['message'] ?? 'Gagal cek tagihan');
    }

    $updatedCount = 0;
    foreach ($result['data'] as $tagihan) {
        if ($tagihan['StatusBayar'] == "1") {
            $peserta = \App\Models\DataPeserta::where('no_pendaftaran', $tagihan['NomorPendaftaran'])->first();

            if ($peserta) {
                $updated = $peserta->where(function ($q) {
                        $q->whereNull('status_pembayaran_registrasi')
                          ->orWhere('status_pembayaran_registrasi', 0);
                    })
                    ->update([
                        'status_pembayaran_registrasi' => 1,
                        'tgl_bayar_regis' => $tagihan['TanggalBayar']
                    ]);

                if ($updated) {
                    \DB::table('tagihan')
                        ->where('id_peserta', $peserta->id)
                        ->update([
                            'status' => 1,
                            'tanggal_pembayaran_daful' => $tagihan['TanggalBayar'],
                            'updated_at' => now()
                        ]);
                    $updatedCount++;
                }
            }
        }
    }

    if ($updatedCount > 0) {
        return back()->with('success', "Berhasil update status {$updatedCount} peserta");
    }

    return back()->with('success', 'Berhasil cek status');
}

public function cekStatusIndex1(Request $request)
{
    $user = Auth::user();
    $role = $user->role;

    $normalizedRole = Str::of($role)->replace('-', ' ')->__toString();
    $roleData = MasterRole::whereRaw('LOWER(nama_role) = ?', [strtolower($normalizedRole)])->first();

    $menus = [];
    if ($roleData && $roleData->menu) {
        if (is_array($roleData->menu)) {
            $menus = array_map(fn($m) => strtolower(trim($m)), $roleData->menu);
        } else {
            $decoded = json_decode($roleData->menu, true);
            if (is_array($decoded)) {
                $menus = array_map(fn($m) => strtolower(trim($m)), $decoded);
            } else {
                $menus = explode(',', strtolower($roleData->menu));
            }
        }
    }

    $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();

    $tahun = $request->get('tahun_akademik');
    $gelombangId = $request->get('gelombang_id');
    $jalurId = $request->get('jalur_id', 'all');

    $gelombangList = collect();
    if ($tahun) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
            ->orderBy('gelombang')
            ->get();
    }

    $jalurList = MasterJalur::orderBy('id')->get();

    $query = DataPeserta::query()->where('status_ujian', 'lulus');

    if ($tahun) {
        $query->whereHas('relasiGelombang', function ($q) use ($tahun) {
            $q->where('tahun_akademik', $tahun);
        });
    }

    if ($gelombangId) {
        $query->where('id_gelombang', $gelombangId);
    }

    if ($jalurId !== 'all') {
        $query->where('id_jalur', $jalurId);
    }

    if ($user->prodi_id) {
        $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        $query->whereIn('id_prodi', $prodiIds);
    }

    $pesertaList = $query->orderBy('id')->paginate(15)->withQueryString();

    return view('dashboard-unit.registrasi-lunas.cek-status', compact(
        'tahunList',
        'gelombangList',
        'tahun',
        'gelombangId',
        'jalurList',
        'jalurId',
        'pesertaList',
        'menus',
        'role'
    ));
}


public function cekStatusIndex(Request $request)
    {
        $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();

        $tahun = $request->get('tahun_akademik');
        $gelombangId = $request->get('gelombang_id');
        $jalurId = $request->get('jalur_id', 'all');

        $gelombangList = collect();
        if ($tahun) {
            $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
                ->orderBy('gelombang')
                ->get();
        }

        $jalurList = MasterJalur::orderBy('id')->get();

        $query = DataPeserta::query()->where('status_ujian', 'lulus');

        if ($tahun) {
            $query->whereHas('relasiGelombang', function ($q) use ($tahun) {
                $q->where('tahun_akademik', $tahun);
            });
        }

        if ($gelombangId) {
            $query->where('id_gelombang', $gelombangId);
        }

        if ($jalurId !== 'all') {
            $query->where('id_jalur', $jalurId);
        }

        $pesertaList = $query->orderBy('id')->paginate(15)->withQueryString();

        return view('dashboard.registrasi-lunas.cek-status', compact(
            'tahunList',
            'gelombangList',
            'tahun',
            'gelombangId',
            'jalurList',
            'jalurId',
            'pesertaList'
        ));
    }

public function registrasiLunas1(Request $request)
{
    $user = Auth::user();
    $role = $user->role;

    $normalizedRole = Str::of($role)->replace('-', ' ')->__toString();
    $roleData = MasterRole::whereRaw('LOWER(nama_role) = ?', [strtolower($normalizedRole)])->first();

    $menus = [];
    if ($roleData && $roleData->menu) {
        if (is_array($roleData->menu)) {
            $menus = array_map(fn($m) => strtolower(trim($m)), $roleData->menu);
        } else {
            $decoded = json_decode($roleData->menu, true);
            if (is_array($decoded)) {
                $menus = array_map(fn($m) => strtolower(trim($m)), $decoded);
            } else {
                $menus = explode(',', strtolower($roleData->menu));
            }
        }
    }

    $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();

    $tahun = $request->get('tahun_akademik'); 
    $gelombangId = $request->get('gelombang_id');
    $jalurId = $request->get('jalur_id', 'all');

    $gelombangList = collect();
    if ($tahun) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
            ->orderBy('gelombang')
            ->get();
    }

    $jalurList = MasterJalur::orderBy('id')->get();

    $query = DataPeserta::with('masterHarga')
        ->where('status_ujian', 'lulus')
        ->where('status_pembayaran_registrasi', 1);

    if ($tahun) {
        $query->whereHas('relasiGelombang', function ($q) use ($tahun) {
            $q->where('tahun_akademik', $tahun);
        });
    }

    if ($gelombangId) {
        $query->where('id_gelombang', $gelombangId);
    }

    if ($jalurId !== 'all') {
        $query->where('id_jalur', $jalurId);
    }

    if ($user->prodi_id) {
        $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        $query->whereIn('id_prodi', $prodiIds);
    }

    if ($request->get('export') === 'excel') {
        return Excel::download(
            new PesertaRegistrasiExport($query->get()),
            'data_psb_all_' . now()->format('d-m-y_H_i_s') . '.xlsx'
        );
    }

    $pesertaList = $query->orderBy('id')->paginate(15)->withQueryString();

    $fieldWajib = ['alamat_lengkap', 'tanggal_lahir', 'ayah_nama', 'ibu_nama'];
    $uploadWajib = ['dokumen_kk', 'dokumen_ijazah', 'dokumen_akte_kelahiran'];

    return view('dashboard-unit.registrasi-lunas.index', compact(
        'tahunList',
        'gelombangList',
        'tahun',
        'gelombangId',
        'jalurList',
        'jalurId',
        'pesertaList',
        'fieldWajib',
        'uploadWajib',
        'menus',
        'role'
    ));
}


    public function registrasiLunas(Request $request)
{
    $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();

    $tahun = $request->get('tahun_akademik'); 
    $gelombangId = $request->get('gelombang_id');
    $jalurId = $request->get('jalur_id', 'all');

    $gelombangList = collect();
    if ($tahun) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
            ->orderBy('gelombang')
            ->get();
    }

    $jalurList = MasterJalur::orderBy('id')->get();

    $query = DataPeserta::with('masterHarga')
        ->where('status_ujian', 'lulus')
        ->where('status_pembayaran_registrasi', 1);

    if ($tahun) {
        $query->whereHas('relasiGelombang', function ($q) use ($tahun) {
            $q->where('tahun_akademik', $tahun);
        });
    }

    if ($gelombangId) {
        $query->where('id_gelombang', $gelombangId);
    }

    if ($jalurId !== 'all') {
        $query->where('id_jalur', $jalurId);
    }

    if ($request->get('export') === 'excel') {
        return Excel::download(
            new PesertaRegistrasiExport($query->get()),
            'data_psb_all_' . now()->format('d-m-y_H_i_s') . '.xlsx'
        );
    }

    $pesertaList = $query->orderBy('id')->paginate(15)->withQueryString();

    $fieldWajib = ['alamat_lengkap', 'tanggal_lahir', 'ayah_nama', 'ibu_nama'];
    $uploadWajib = ['dokumen_kk', 'dokumen_ijazah', 'dokumen_akte_kelahiran'];

    return view('dashboard.registrasi-lunas.index', compact(
        'tahunList',
        'gelombangList',
        'tahun',
        'gelombangId',
        'jalurList',
        'jalurId',
        'pesertaList',
        'fieldWajib',
        'uploadWajib'
    ));
}

public function index()
{
    $today = now()->toDateString();

    $jalurs = MasterHarga::where('active', 1)
        ->whereHas('gelombang', function ($q) use ($today) {
    $q->whereDate('end', '>=', $today);
})
        ->select('id_jalur', 'nama_jalur')
        ->distinct()
        ->get();

    $gelombangs   = collect();
    $fakultas     = collect();
    $prodis       = collect();
    $jurusans     = MasterJurusanSekolah::orderBy('nama')->get();
    $pekerjaans   = MasterPekerjaanOrtu::orderBy('id')->get();
    $penghasilans = MasterPenghasilanOrtu::orderBy('id')->get();

    return view('auth.register', compact(
        'jalurs',
        'gelombangs',
        'fakultas',
        'prodis',
        'jurusans',
        'pekerjaans',
        'penghasilans'
    ));
}


   public function getProdi($fakultasId)
{
    $prodis = MasterHarga::with('prodi')
        ->where('id_fakultas', $fakultasId)
        ->select('id_prodi')
        ->distinct()
        ->get()
        ->pluck('prodi')
        ->filter();

    return response()->json($prodis);
}

}
