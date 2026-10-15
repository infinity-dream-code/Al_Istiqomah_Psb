<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\DataPeserta;
use App\Models\MasterUjian;
use App\Models\Tagihan;
use App\Models\MasterRuang;
use App\Models\Ujian;
use App\Models\MasterGelombang;
use App\Models\MasterRole;
use App\Models\MasterProdi;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class UjianController extends Controller
{
 public function simpan1(Request $request)
    {
        $request->validate([
            'peserta_ids' => 'required|array|min:1',
            'ujian'       => 'required|array',
        ]);

        foreach ($request->peserta_ids as $pesertaId) {
            foreach ($request->ujian as $id_master_ujian => $data) {
                if (!empty($data['tanggal']) && !empty($data['ruang'])) {
                    Ujian::updateOrCreate(
                        [
                            'id_peserta'      => $pesertaId,
                            'id_master_ujian' => $id_master_ujian,
                        ],
                        [
                            'tanggal' => $data['tanggal'],
                            'ruang'   => $data['ruang'],
                        ]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Parameter ujian berhasil disimpan']);
    }

public function setKelulusanPeserta1(Request $request, $status)
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

    if (empty($request->get('ids'))) {
        return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih.');
    }

    $ids = explode(',', $request->get('ids'));

    if ($status === 'gagal') {
        DataPeserta::whereIn('id', $ids)->update([
            'status_ujian' => 'gagal',
            'batas_awal_registrasi' => null,
            'batas_akhir_registrasi' => null,
            'pembekalan' => null,
        ]);

        Tagihan::whereIn('id_peserta', $ids)->delete();
    } 
    elseif ($status === 'lulus') {
        DataPeserta::whereIn('id', $ids)->update([
            'status_ujian' => 'lulus',
            'batas_awal_registrasi' => $request->awal,
            'batas_akhir_registrasi' => $request->akhir,
            'pembekalan' => $request->pembekalan,
        ]);

        $pesertaList = DataPeserta::with('masterHarga', 'relasiGelombang')
            ->whereIn('id', $ids)
            ->get();

        $payloadData = [];

        foreach ($pesertaList as $peserta) {
            $detail = [];
            $biayaDaful = 0;

            if ($peserta->masterHarga && is_array($peserta->masterHarga->detail)) {
                $detail = collect($peserta->masterHarga->detail)->map(function($d) {
                    return [
                        "nama_tagihan" => $d['nama_tagihan'],
                        "kode_tagihan" => $d['no_akun'],
                        "nominal"      => (int) $d['biaya'],
                    ];
                })->toArray();

                $biayaDaful = array_sum(array_column($detail,'nominal'));
            }

            Tagihan::create([
                'id_peserta'              => $peserta->id,
                'biaya_daful'             => $biayaDaful,
                'detail'                  => $detail,
                'status'                  => null,
                'tanggal_pembayaran_daful'=> null,
            ]);

            $payloadData[] = [
                "siswa" => [
                    "nomor_pendaftaran" => $peserta->no_pendaftaran,
                ],
                "tagihan" => [
                    "periode"        => now()->format('Ym'),
                    "nama_tagihan"   => "TAGIHAN_PENDAFTARAN_ULANG",
                    "tahun_akademik" => optional($peserta->relasiGelombang)->tahun_akademik,
                    "detail_tagihan" => $detail
                ]
            ];
        }

        if (!empty($payloadData)) {
            $jwtToken = JWT::encode(
                ['data' => $payloadData],
                '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4',
                'HS256'
            );

            $payload = [
                "token"  => $jwtToken,
                "method" => "CreateTagihanBulk"
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('http://10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', $payload);

            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Gagal membuat tagihan');
            }
        }
    }

    return redirect()->back()->with('success', 'Status ujian berhasil diperbarui');
}




    public function setKelulusan1(Request $request)
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

        $query = DataPeserta::query();

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        }

        if (!empty($prodiIds)) {
            $fakultasIds = MasterProdi::whereIn('id', $prodiIds)
                ->pluck('id_fakultas')
                ->unique()
                ->toArray();

            $query->whereIn('id_fakultas', $fakultasIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_peserta', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('fakultas', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('jalur', 'like', "%$search%");
            });
        }

        $today = now()->toDateString();

        $tahunAkademikList = MasterGelombang::select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik')
            ->get();

        if ($request->filled('tahun_akademik')) {
            $gelombangList = MasterGelombang::where('tahun_akademik', $request->tahun_akademik)
                ->where('end', '>=', $today)
                ->orderBy('gelombang')
                ->get();
        } else {
            $gelombangList = MasterGelombang::where('end', '>=', $today)
                ->orderBy('gelombang')
                ->get();
        }

        $defaultGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

        if ($idGelombang) {
            $query->where('id_gelombang', $idGelombang);
        }

        $fieldWajib = ['no_hp','nik','no_kk','kewarganegaraan'];
        $uploadWajib = ['foto','dokumen_kk','dokumen_akte_kelahiran'];

        $pesertaList = $query->get()->filter(function ($peserta) use ($fieldWajib) {
            foreach ($fieldWajib as $field) {
                if (empty($peserta->$field)) {
                    return false;
                }
            }
            return !empty($peserta->foto);
        });

        $perPage = 10;
        $page = request()->get('page', 1);
        $items = $pesertaList->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedPeserta = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $pesertaList->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $masterUjianList = MasterUjian::all();

        return view('dashboard-unit.kelulusan.index', compact(
            'paginatedPeserta',
            'fieldWajib',
            'uploadWajib',
            'masterUjianList',
            'gelombangList',
            'tahunAkademikList',
            'idGelombang',
            'menus',
            'role'
        ));
    }

    public function cekBerkasSetUjian2(Request $request)
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

        $query = DataPeserta::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_peserta', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('fakultas', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('jalur', 'like', "%$search%");
            });
        }

        $today = now()->toDateString();

        $tahunAkademikList = MasterGelombang::select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik')
            ->get();

        $gelombangQuery = MasterGelombang::query();
        if ($request->filled('tahun_akademik')) {
            $gelombangQuery->where('tahun_akademik', $request->tahun_akademik);
        }
        $gelombangQuery->where('end', '>=', $today);
        $gelombangList = $gelombangQuery->orderBy('gelombang')->get();

        $defaultGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

        if ($idGelombang) {
            $query->where('id_gelombang', $idGelombang);
        }

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
            $query->whereIn('id_prodi', $prodiIds);
        }

        $fieldWajib = [
            'no_hp',
            'nik',
            'no_kk',
            'kewarganegaraan',
        ];

        $uploadWajib = [
            'foto',
            'dokumen_kk',
            'dokumen_akte_kelahiran',
        ];

        $pesertaList = $query->orderBy('created_at', 'desc')->paginate(10);
        $pesertaList->appends($request->only(['tahun_akademik', 'gelombang_id', 'search']));

        $pesertaList->getCollection()->transform(function ($peserta) use ($fieldWajib, $uploadWajib) {
            $lengkap = true;
            foreach ($fieldWajib as $f) {
                if (empty($peserta->$f)) {
                    $lengkap = false;
                    break;
                }
            }
            if ($lengkap) {
                foreach ($uploadWajib as $f) {
                    if (empty($peserta->$f)) {
                        $lengkap = false;
                        break;
                    }
                }
            }
            $peserta->is_lengkap = $lengkap;
            $peserta->is_paid = (bool) $peserta->status_paid;
            return $peserta;
        });

        $paginatedPeserta = $pesertaList;

        $masterUjianList = MasterUjian::all();
        $masterRuangList = MasterRuang::all();

        return view('dashboard-unit.ujian.cek_berkas_set_ujian', compact(
            'paginatedPeserta',
            'fieldWajib',
            'uploadWajib',
            'masterUjianList',
            'masterRuangList',
            'gelombangList',
            'tahunAkademikList',
            'idGelombang',
            'menus',
            'role'
        ));
    }

    public function cetakKartu1($no_pendaftaran)
    {
        $noList = explode(',', $no_pendaftaran);

        $pesertaList = DataPeserta::with(['relasiGelombang', 'ujian.masterUjian'])
            ->whereIn('no_pendaftaran', $noList)
            ->get();

        $masterUjian = \App\Models\MasterUjian::all();

        if ($pesertaList->isEmpty()) {
            abort(404, 'Peserta tidak ditemukan');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard-unit.ujian.kartu-ujian', compact('pesertaList', 'masterUjian'))
                  ->setPaper('A4', 'portrait');

        return $pdf->stream('kartu-ujian.pdf', ['Attachment' => false]);
    }

    public function cetakKartuUjian1(Request $request)
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

        $query = DataPeserta::with(['relasiGelombang', 'ujian.masterUjian'])
            ->whereHas('ujian');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_peserta', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('fakultas', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('jalur', 'like', "%$search%");
            });
        }

        $today = now()->toDateString();

        $tahunAkademikList = MasterGelombang::select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik')
            ->get();

        $gelombangQuery = MasterGelombang::query();
        if ($request->filled('tahun_akademik')) {
            $gelombangQuery->where('tahun_akademik', $request->tahun_akademik);
        }
        $gelombangQuery->where('end', '>=', $today);
        $gelombangList = $gelombangQuery->orderBy('gelombang')->get();

        $defaultGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

        if ($idGelombang) {
            $query->where('id_gelombang', $idGelombang);
        }

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
            $query->whereIn('id_prodi', $prodiIds);
        }

        $pesertaList = $query->orderBy('created_at', 'desc')->paginate(10);
        $pesertaList->appends($request->only(['tahun_akademik', 'gelombang_id', 'search']));

        return view('dashboard-unit.ujian.cetak-kartu-ujian', compact(
            'pesertaList',
            'gelombangList',
            'tahunAkademikList',
            'idGelombang',
            'menus',
            'role'
        ));
    }

    public function editJadwalUjian1(Request $request)
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

        $query = DataPeserta::with(['ujian' => function ($q) {
            $q->with('masterUjian')
              ->select('id', 'id_peserta', 'id_master_ujian', 'tanggal', 'ruang');
        }])
        ->whereHas('ujian')
        ->whereHas('relasiGelombang', function ($q) {
            $q->whereDate('end', '>=', now()->toDateString());
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peserta', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('fakultas', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('jalur', 'like', "%$search%");
            });
        }

        $today = now()->toDateString();

        $tahunAkademikList = MasterGelombang::select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik')
            ->get();

        $gelombangQuery = MasterGelombang::query();
        if ($request->filled('tahun_akademik')) {
            $gelombangQuery->where('tahun_akademik', $request->tahun_akademik);
        }
        $gelombangQuery->where('end', '>=', $today);
        $gelombangList = $gelombangQuery->orderBy('gelombang')->get();

        $defaultGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

        if ($idGelombang) {
            $query->where('id_gelombang', $idGelombang);
        }

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
            $query->whereIn('id_prodi', $prodiIds);
        }

        $pesertaList = $query->orderBy('created_at', 'desc')->paginate(10);
        $pesertaList->appends($request->only(['tahun_akademik', 'gelombang_id', 'search']));

        $masterUjianList = MasterUjian::all();
        $masterRuangList = MasterRuang::all();

        return view('dashboard-unit.ujian.edit-jadwal', compact(
            'pesertaList',
            'gelombangList',
            'tahunAkademikList',
            'idGelombang',
            'masterUjianList',
            'masterRuangList',
            'menus',
            'role'
        ));
    }

    public function updateJadwalUjian1(Request $request)
    {
        $jadwals = $request->input('jadwal', []);

        foreach ($jadwals as $pesertaId => $ujianList) {
            foreach ($ujianList as $ujianId => $data) {
                Ujian::where('id', $ujianId)
                    ->where('id_peserta', $pesertaId)
                    ->update([
                        'tanggal' => $data['tanggal'] ?? null,
                        'ruang'   => $data['ruang'] ?? null,
                    ]);
            }
        }

        return redirect()->back()->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function simpan(Request $request)
    {
        
        $request->validate([
            'peserta_ids' => 'required|array|min:1',
            'ujian'       => 'required|array',
        ]);

        foreach ($request->peserta_ids as $pesertaId) {
            foreach ($request->ujian as $id_master_ujian => $data) {
                if (!empty($data['tanggal']) && !empty($data['ruang'])) {
                    Ujian::updateOrCreate(
                        [
                            'id_peserta'      => $pesertaId,
                            'id_master_ujian' => $id_master_ujian,
                        ],
                        [
                            'tanggal' => $data['tanggal'],
                            'ruang'   => $data['ruang'],
                        ]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Parameter ujian berhasil disimpan']);
    }

public function setKelulusanPeserta(Request $request, $status)
{
    $ids = explode(',', $request->get('ids'));

    if ($status === 'gagal') {
        DataPeserta::whereIn('id', $ids)->update([
            'status_ujian' => 'gagal',
            'batas_awal_registrasi' => null,
            'batas_akhir_registrasi' => null,
            'pembekalan' => null,
        ]);

        Tagihan::whereIn('id_peserta', $ids)->delete();
    } 
    elseif ($status === 'lulus') {
        DataPeserta::whereIn('id', $ids)->update([
            'status_ujian' => 'lulus',
            'batas_awal_registrasi' => $request->awal,
            'batas_akhir_registrasi' => $request->akhir,
            'pembekalan' => $request->pembekalan,
        ]);

        $pesertaList = DataPeserta::with('masterHarga','relasiGelombang')
            ->whereIn('id', $ids)
            ->get();

        $payloadData = [];

        foreach ($pesertaList as $peserta) {
            $detail = [];
            $biayaDaful = 0;

            if ($peserta->masterHarga && is_array($peserta->masterHarga->detail)) {
                $detail = collect($peserta->masterHarga->detail)->map(function($d) {
                    return [
                        "nama_tagihan" => $d['nama_tagihan'],
                        "kode_tagihan" => $d['no_akun'],
                        "nominal"      => (int) $d['biaya'],
                    ];
                })->toArray();

                $biayaDaful = array_sum(array_column($detail,'nominal'));
            } else {
                Log::warning('Peserta tanpa masterHarga', [
                    'id_peserta' => $peserta->id,
                    'id_master_harga' => $peserta->id_master_harga
                ]);
            }

            Tagihan::create([
                'id_peserta'              => $peserta->id,
                'biaya_daful'             => $biayaDaful,
                'detail'                  => $detail,
                'status'                  => null,
                'tanggal_pembayaran_daful'=> null,
            ]);

            $payloadData[] = [
                "siswa" => [
                    "nomor_pendaftaran" => $peserta->no_pendaftaran,
                ],
                "tagihan" => [
                    "periode"        => now()->format('Ym'),
                    "nama_tagihan"   => "TAGIHAN_PENDAFTARAN_ULANG",
                    "tahun_akademik" => optional($peserta->relasiGelombang)->tahun_akademik,
                    "detail_tagihan" => $detail
                ]
            ];
        }

        if (!empty($payloadData)) {
            $jwtToken = JWT::encode(
                ['data' => $payloadData],
                '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4',
                'HS256'
            );

            $payload = [
                "token"  => $jwtToken,
                "method" => "CreateTagihanBulk"
            ];

            Log::info('CreateTagihanBulk payload', $payload);

            $response = \Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', $payload);

            Log::info('CreateTagihanBulk response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Gagal membuat tagihan');
            }
        }
    }

    return redirect()->back()->with('success', 'Status ujian berhasil diperbarui');
}


   public function setKelulusan(Request $request)
{
    $query = DataPeserta::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_peserta', 'like', "%$search%")
              ->orWhere('no_pendaftaran', 'like', "%$search%")
              ->orWhere('fakultas', 'like', "%$search%")
              ->orWhere('prodi', 'like', "%$search%")
              ->orWhere('jalur', 'like', "%$search%");
        });
    }

    $today = now()->toDateString();

    $tahunAkademikList = MasterGelombang::select('tahun_akademik')
        ->distinct()
        ->orderBy('tahun_akademik')
        ->get();

    if ($request->filled('tahun_akademik')) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $request->tahun_akademik)
            ->where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    } else {
        $gelombangList = MasterGelombang::where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    }

    $defaultGelombang = MasterGelombang::where('start', '<=', $today)
        ->where('end', '>=', $today)
        ->first();

    $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

    if ($idGelombang) {
        $query->where('id_gelombang', $idGelombang);
    }

    $fieldWajib = [
        'no_hp',
        'nik',
        'no_kk',
        'kewarganegaraan',
    ];

    $uploadWajib = [
        'foto',
        'dokumen_kk',
        'dokumen_akte_kelahiran',
    ];

    $pesertaList = $query->get()->filter(function ($peserta) use ($fieldWajib) {
        foreach ($fieldWajib as $field) {
            if (empty($peserta->$field)) {
                return false;
            }
        }
        return !empty($peserta->foto);
    });

    $perPage = 10;
    $page = request()->get('page', 1);
    $items = $pesertaList->slice(($page - 1) * $perPage, $perPage)->values();
    $paginatedPeserta = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $pesertaList->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    $masterUjianList = MasterUjian::all();

    return view('dashboard.kelulusan.index', compact(
        'paginatedPeserta',
        'fieldWajib',
        'uploadWajib',
        'masterUjianList',
        'gelombangList',
        'tahunAkademikList',
        'idGelombang'
    ));
}


public function cekBerkasSetUjian(Request $request) 
{
    $query = DataPeserta::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_peserta', 'like', "%$search%")
              ->orWhere('no_pendaftaran', 'like', "%$search%")
              ->orWhere('fakultas', 'like', "%$search%")
              ->orWhere('prodi', 'like', "%$search%")
              ->orWhere('jalur', 'like', "%$search%");
        });
    }

    $today = now()->toDateString();

    $tahunAkademikList = MasterGelombang::select('tahun_akademik')
        ->distinct()
        ->orderBy('tahun_akademik')
        ->get();

    if ($request->filled('tahun_akademik')) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $request->tahun_akademik)
            ->where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    } else {
        $gelombangList = MasterGelombang::where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    }

    $defaultGelombang = MasterGelombang::where('start', '<=', $today)
        ->where('end', '>=', $today)
        ->first();

    $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

    if ($idGelombang) {
        $query->where('id_gelombang', $idGelombang);
    }

    $fieldWajib = [
        'no_hp',
        'nik',
        'no_kk',
        'kewarganegaraan',
    ];

    $uploadWajib = [
        'foto',
        'dokumen_kk',
        'dokumen_akte_kelahiran',
    ];

    $pesertaList = $query->get()->filter(function ($peserta) use ($fieldWajib) {
        foreach ($fieldWajib as $field) {
            if (empty($peserta->$field)) {
                return false;
            }
        }
        if (empty($peserta->foto)) {
            return false;
        }
        return true;
    });

    $perPage = 10;
    $page = request()->get('page', 1);
    $items = $pesertaList->slice(($page - 1) * $perPage, $perPage)->values();
    $paginatedPeserta = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $pesertaList->count(),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    $masterUjianList = MasterUjian::all();
    $masterRuangList = MasterRuang::all();

    return view('dashboard.ujian.cek_berkas_set_ujian', compact(
        'paginatedPeserta',
        'fieldWajib',
        'uploadWajib',
        'masterUjianList',
        'masterRuangList',
        'gelombangList',
        'tahunAkademikList',
        'idGelombang'
    ));
}




public function cetakKartu($no_pendaftaran)
{
    $noList = explode(',', $no_pendaftaran);

    $pesertaList = DataPeserta::with(['relasiGelombang', 'ujian.masterUjian'])
        ->whereIn('no_pendaftaran', $noList)
        ->get();

    $masterUjian = \App\Models\MasterUjian::all();

    if ($pesertaList->isEmpty()) {
        abort(404, 'Peserta tidak ditemukan');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.ujian.kartu-ujian', compact('pesertaList', 'masterUjian'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream('kartu-ujian.pdf', ['Attachment' => false]);
}

public function cetakKartuUjian(Request $request)
{
    $query = DataPeserta::with(['relasiGelombang', 'ujian.masterUjian'])
        ->whereHas('ujian');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_peserta', 'like', "%$search%")
              ->orWhere('no_pendaftaran', 'like', "%$search%")
              ->orWhere('fakultas', 'like', "%$search%")
              ->orWhere('prodi', 'like', "%$search%")
              ->orWhere('jalur', 'like', "%$search%");
        });
    }

    $today = now()->toDateString();

    $tahunAkademikList = MasterGelombang::select('tahun_akademik')
        ->distinct()
        ->orderBy('tahun_akademik')
        ->get();

    if ($request->filled('tahun_akademik')) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $request->tahun_akademik)
            ->where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    } else {
        $gelombangList = MasterGelombang::where('end', '>=', $today)
            ->orderBy('gelombang')
            ->get();
    }

    $defaultGelombang = MasterGelombang::where('start', '<=', $today)
        ->where('end', '>=', $today)
        ->first();

    $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

    if ($idGelombang) {
        $query->where('id_gelombang', $idGelombang);
    }

    $pesertaList = $query->paginate(10)->appends($request->query());

    return view('dashboard.ujian.cetak-kartu-ujian', compact(
        'pesertaList',
        'gelombangList',
        'tahunAkademikList',
        'idGelombang'
    ));
}


public function editJadwalUjian(Request $request)
    {
        $query = DataPeserta::with(['ujian.masterUjian'])
            ->whereHas('ujian')
            ->whereHas('relasiGelombang', function($q) {
                $q->whereDate('end', '>=', now()->toDateString());
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_peserta', 'like', "%$search%")
                  ->orWhere('no_pendaftaran', 'like', "%$search%")
                  ->orWhere('fakultas', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%")
                  ->orWhere('jalur', 'like', "%$search%");
            });
        }

        $today = now()->toDateString();

        $tahunAkademikList = MasterGelombang::select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik')
            ->get();

        if ($request->filled('tahun_akademik')) {
            $gelombangList = MasterGelombang::where('tahun_akademik', $request->tahun_akademik)
                ->where('end', '>=', $today)
                ->orderBy('gelombang')
                ->get();
        } else {
            $gelombangList = MasterGelombang::where('end', '>=', $today)
                ->orderBy('gelombang')
                ->get();
        }

        $defaultGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        $idGelombang = $request->get('gelombang_id', $defaultGelombang?->id);

        if ($idGelombang) {
            $query->where('id_gelombang', $idGelombang);
        }

        $pesertaList = $query->paginate(10)->appends($request->query());

        $ruangList = MasterRuang::select('id','ruang')->orderBy('ruang')->pluck('ruang')->toArray();

        return view('dashboard.ujian.edit-jadwal', compact(
            'pesertaList',
            'gelombangList',
            'tahunAkademikList',
            'idGelombang',
            'ruangList'
        ));
    }


    public function updateJadwalUjian(Request $request)
    {
        $jadwals = $request->input('jadwal', []);

        foreach ($jadwals as $pesertaId => $ujianList) {
            foreach ($ujianList as $ujianId => $data) {
                Ujian::where('id', $ujianId)
                    ->where('id_peserta', $pesertaId)
                    ->update([
                        'tanggal' => $data['tanggal'] ?? null,
                        'ruang'   => $data['ruang'] ?? null,
                    ]);
            }
        }

        return redirect()->back()->with('success', 'Jadwal ujian berhasil diperbarui.');
    }



}
