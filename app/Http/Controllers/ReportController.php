<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeserta;
use App\Models\MasterGelombang;
use Illuminate\Support\Facades\DB;
use App\Models\MasterHarga;
use App\Models\DataBantuan;
use App\Models\MasterRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Exports\DetailBiayaExport;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\DataPsbAllExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{

    private function getMenusAndRole()
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
        return [$menus, $role];
    }

    private function getProdiIds()
    {
        $user = Auth::user();
        if (!$user) return [];
        if (is_array($user->prodi_id)) return $user->prodi_id;
        return explode(',', $user->prodi_id);
    }

    public function rekapJumlahPendaftar1(Request $request)
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
        $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
        $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);
        $rekap = DataPeserta::select('kabupaten as kota', DB::raw('COUNT(*) as jumlah'))
            ->whereIn('id_prodi', $prodiIds)
            ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
            ->groupBy('kabupaten')
            ->orderBy('jumlah', 'desc')
            ->get();
        $peserta = DataPeserta::whereIn('id_prodi', $prodiIds)
            ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
            ->get();
        return view('dashboard-unit.report.jumlah-pendaftar', compact('tahunList', 'gelombangList', 'tahun', 'gelombangId', 'rekap', 'peserta', 'menus', 'role'));
    }

    public function rekapLunasPendaftaran1(Request $request)
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
        $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
        $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);
        $peserta = DataPeserta::with('masterHarga')
            ->whereIn('id_prodi', $prodiIds)
            ->where('status_paid', 1)
            ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('dashboard-unit.report.lunas-pendaftaran', compact('tahunList', 'gelombangList', 'tahun', 'gelombangId', 'peserta', 'menus', 'role'));
    }

    public function rekapLunasRegistrasi1(Request $request)
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
        $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
        $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);
        $peserta = DataPeserta::with(['masterHarga', 'relasiGelombang'])
            ->whereIn('id_prodi', $prodiIds)
            ->where('status_pembayaran_registrasi', 1)
            ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
            ->get();
        return view('dashboard-unit.report.lunas-registrasi', compact('tahunList', 'gelombangList', 'tahun', 'gelombangId', 'peserta', 'menus', 'role'));
    }

    public function siswaPerProvinsi1()
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunSekarang = Carbon::now()->year;
        $rekap = DataPeserta::select('provinsi', DB::raw('COUNT(*) as jumlah'))
            ->whereIn('id_prodi', $prodiIds)
            ->whereHas('relasiGelombang', fn($q) => $q->where('tahun', $tahunSekarang))
            ->groupBy('provinsi')
            ->orderByDesc('jumlah')
            ->get();
        return view('dashboard-unit.report.siswa-provinsi', compact('rekap', 'tahunSekarang', 'menus', 'role'));
    }

    public function siswaPerKota1()
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunSekarang = Carbon::now()->year;
        $rekap = DataPeserta::select('kabupaten', DB::raw('COUNT(*) as jumlah'))
            ->whereIn('id_prodi', $prodiIds)
            ->whereHas('relasiGelombang', fn($q) => $q->where('tahun', $tahunSekarang))
            ->groupBy('kabupaten')
            ->orderByDesc('jumlah')
            ->get();
        return view('dashboard-unit.report.siswa-kota', compact('rekap', 'tahunSekarang', 'menus', 'role'));
    }

    public function siswaPerProdi1()
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunSekarang = Carbon::now()->year;
        $rekap = DataPeserta::select('id_prodi', DB::raw('COUNT(*) as jumlah'))
            ->whereIn('id_prodi', $prodiIds)
            ->whereHas('relasiGelombang', fn($q) => $q->where('tahun', $tahunSekarang))
            ->groupBy('id_prodi')
            ->orderByDesc('jumlah')
            ->get();
        return view('dashboard-unit.report.siswa-prodi', compact('rekap', 'tahunSekarang', 'menus', 'role'));
    }

    public function siswaPerSekolah1()
    {
        [$menus, $role] = $this->getMenusAndRole();
        $prodiIds = $this->getProdiIds();
        $tahunSekarang = Carbon::now()->year;
        $rekap = DataPeserta::select('nama_sekolah', DB::raw('COUNT(*) as jumlah'))
            ->whereIn('id_prodi', $prodiIds)
            ->whereHas('relasiGelombang', fn($q) => $q->where('tahun', $tahunSekarang))
            ->groupBy('nama_sekolah')
            ->orderByDesc('jumlah')
            ->get();
        return view('dashboard-unit.report.siswa-sekolah', compact('rekap', 'tahunSekarang', 'menus', 'role'));
    }

    public function exportDetailBiaya1()
    {
        $fileName = 'data_biaya_psb_' . now()->format('d-m-Y_H-i-s') . '.xlsx';
        return Excel::download(new DetailBiayaExport, $fileName);
    }

    public function exportAllpsb1(Request $request)
    {
        $prodiIds = $this->getProdiIds();
        $tahun = $request->get('tahun');
        $idGelombang = $request->get('gelombang_id');
        $search = $request->get('search');
        $fileName = 'data_psb_unit_' . now()->format('d-m-Y_H-i-s') . '.xlsx';
        return Excel::download(new DataPsbAllExport($tahun, $idGelombang, $search, $prodiIds), $fileName);
    }

   public function exportAll1(Request $request)
{
    [$menus, $role] = $this->getMenusAndRole();
    $prodiIds = $this->getProdiIds();
    $tahun = $request->get('tahun');
    $idGelombang = $request->get('gelombang_id');
    $search = $request->get('search');

    $tahunList = MasterGelombang::select('tahun_akademik')
        ->distinct()
        ->orderBy('tahun_akademik', 'desc')
        ->get();

    $gelombangList = collect();
    if ($tahun) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
            ->orderBy('gelombang')
            ->get();
    }

    $pesertaQuery = DataPeserta::whereIn('id_prodi', $prodiIds);

    if ($tahun) {
        $idsGelByTahun = MasterGelombang::where('tahun_akademik', $tahun)->pluck('id');
        $pesertaQuery->whereIn('id_gelombang', $idsGelByTahun);
    }

    if ($idGelombang) {
        $pesertaQuery->where('id_gelombang', $idGelombang);
    }

    if ($search) {
        $pesertaQuery->where(function ($q) use ($search) {
            $q->where('nama_peserta', 'like', "%$search%")
              ->orWhere('no_pendaftaran', 'like', "%$search%")
              ->orWhere('jalur', 'like', "%$search%")
              ->orWhere('fakultas', 'like', "%$search%")
              ->orWhere('prodi', 'like', "%$search%")
              ->orWhere('gelombang', 'like', "%$search%");
        });
    }

    $pesertas = $pesertaQuery->paginate(10);

    $rows = $pesertas->map(function ($p) {
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
            'Program' => $p->prodi ?? '-',
            'Unit' => $p->fakultas ?? '-',
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
            'Bayar Pendaftaran' => $p->status_paid == 1 ? 'Lunas' : 'Belum Lunas',
            'Bayar Registrasi' => $bayarRegistrasi,
            'Nominal Registrasi' => $masterHarga ? $masterHarga->harga_registrasi : '-',
            'Password Login' => $p->plain_password ?? '-',
        ];
    });

    return view('dashboard-unit.report.export-all', [
        'tahunList' => $tahunList,
        'gelombangList' => $gelombangList,
        'tahun' => $tahun,
        'idGelombang' => $idGelombang,
        'search' => $search,
        'pesertas' => $pesertas,
        'rows' => $rows,
        'menus' => $menus,
        'role' => $role,
    ]);
}


public function rekapJumlahPendaftar(Request $request)
    {
        $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
        $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);

        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
        $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);

        $rekap = DataPeserta::select('data_peserta.kabupaten as kota', DB::raw('COUNT(*) as jumlah'))
            ->join('master_gelombang', 'data_peserta.id_gelombang', '=', 'master_gelombang.id')
            ->where('data_peserta.status_ujian', 'lulus')
            ->when($tahun, fn($q) => $q->where('master_gelombang.tahun_akademik', $tahun))
            ->when($gelombangId, fn($q) => $q->where('data_peserta.id_gelombang', $gelombangId))
            ->groupBy('data_peserta.kabupaten')
            ->orderBy('jumlah', 'desc')
            ->get();

        $peserta = DataPeserta::where('status_ujian', 'lulus')
            ->when($tahun, fn($q) => $q->whereHas('gelombang', fn($g) => $g->where('tahun_akademik', $tahun)))
            ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
            ->get();

        return view('dashboard.report.jumlah-pendaftar', compact(
            'tahunList',
            'gelombangList',
            'tahun',
            'gelombangId',
            'rekap',
            'peserta'
        ));
    }

    public function printLp003($ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::with('gelombang')
        ->whereIn('id', $idsArray)
        ->orderBy('created_at', 'asc')
        ->get();

    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->gelombang?->tahun_akademik ?? now()->year . '/' . (now()->year+1);

    $chunks = $peserta->chunk(17);

    return view('dashboard.report.print-lp003', compact('peserta', 'today', 'tahun', 'chunks'));
}

public function rekapLunasPendaftaran(Request $request)
{
    $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
    $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);

    $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
    $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);

    $peserta = DataPeserta::with('masterHarga')
        ->where('status_paid', 1)
        ->when($tahun, fn($q) => $q->whereHas('gelombang', fn($g) => $g->where('tahun_akademik', $tahun)))
        ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('dashboard.report.lunas-pendaftaran', compact(
        'tahunList', 'gelombangList', 'tahun', 'gelombangId', 'peserta'
    ));
}

public function printLp004($ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::with('masterHarga')
        ->whereIn('id', $idsArray)
        ->where('status_paid', 1)
        ->orderBy('created_at', 'asc')
        ->get();

    $chunks = $peserta->chunk(17); 
    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->gelombangModel->tahun_akademik ?? date('Y');

    return view('dashboard.report.print-lp004', compact('peserta', 'chunks', 'today', 'tahun'));
}

public function rekapLunasRegistrasi(Request $request)
{
    $tahunList = MasterGelombang::select('tahun_akademik')->distinct()->get();
    $tahun = $request->get('tahun', $tahunList->first()?->tahun_akademik);

    $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)->get();
    $gelombangId = $request->get('gelombang_id', $gelombangList->first()?->id);

    $peserta = DataPeserta::with(['masterHarga','relasiGelombang'])
        ->where('status_pembayaran_registrasi', 1)
        ->when($tahun, function($q) use ($tahun) {
            $q->whereHas('relasiGelombang', fn($g) => $g->where('tahun_akademik', $tahun));
        })
        ->when($gelombangId, fn($q) => $q->where('id_gelombang', $gelombangId))
        ->orderBy('created_at','asc')
        ->get();

    return view('dashboard.report.lunas-registrasi', compact(
        'tahunList',
        'gelombangList',
        'tahun',
        'gelombangId',
        'peserta'
    ));
}


public function printLp007($ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::whereIn('id', $idsArray)
        ->where('status_pembayaran_registrasi', 1)
        ->orderBy('created_at','asc')
        ->get();

    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->relasiGelombang->tahun_akademik ?? date('Y');

    $chunks = $peserta->chunk(17);

    return view('dashboard.report.print-lp007', compact('peserta', 'today', 'tahun', 'chunks'));
}

public function printLp0031($role, $ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::with('gelombang')
        ->whereIn('id', $idsArray)
        ->orderBy('created_at', 'asc')
        ->get();

    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->gelombang?->tahun_akademik ?? now()->year . '/' . (now()->year + 1);

    $chunks = $peserta->chunk(17);

    return view('dashboard-unit.report.print-lp003', compact('peserta', 'today', 'tahun', 'chunks', 'role'));
}

public function printLp0041($role, $ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::with('masterHarga')
        ->whereIn('id', $idsArray)
        ->where('status_paid', 1)
        ->orderBy('created_at', 'asc')
        ->get();

    $chunks = $peserta->chunk(17);
    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->gelombangModel->tahun_akademik ?? date('Y');

    return view('dashboard-unit.report.print-lp004', compact('peserta', 'chunks', 'today', 'tahun', 'role'));
}

public function printLp0071($role, $ids)
{
    $idsArray = explode(',', $ids);

    $peserta = DataPeserta::whereIn('id', $idsArray)
        ->where('status_pembayaran_registrasi', 1)
        ->orderBy('created_at', 'asc')
        ->get();

    $today = now()->translatedFormat('d M Y');
    $tahun = $peserta->first()?->relasiGelombang->tahun_akademik ?? date('Y');

    $chunks = $peserta->chunk(17);

    return view('dashboard-unit.report.print-lp007', compact('peserta', 'today', 'tahun', 'chunks', 'role'));
}


public function siswaPerProvinsi()
{
     $tahunSekarang = Carbon::now()->year;
    $rekap = DataPeserta::select('provinsi', \DB::raw('COUNT(*) as jumlah'))
        ->whereHas('relasiGelombang', function($q) use ($tahunSekarang) {
            $q->where('tahun', $tahunSekarang);
        })
        ->groupBy('provinsi')
        ->orderByDesc('jumlah')
        ->get();

    return view('dashboard.report.siswa-provinsi', compact('rekap', 'tahunSekarang'));
}


public function siswaPerKota()
{
    $tahunSekarang = Carbon::now()->year;

    $rekap = DataPeserta::select('kabupaten', \DB::raw('COUNT(*) as jumlah'))
        ->whereHas('relasiGelombang', function($q) use ($tahunSekarang) {
            $q->where('tahun', $tahunSekarang);
        })
        ->groupBy('kabupaten')
        ->orderByDesc('jumlah')
        ->get();

    return view('dashboard.report.siswa-kota', compact('rekap', 'tahunSekarang'));
}


public function siswaPerProdi()
{
    $tahunSekarang = Carbon::now()->year;

    $rekap = DataPeserta::with('prodi')
        ->select('id_prodi', DB::raw('COUNT(*) as jumlah'))
        ->whereHas('relasiGelombang', function($q) use ($tahunSekarang) {
            $q->where('tahun', $tahunSekarang);
        })
        ->groupBy('id_prodi')
        ->orderByDesc('jumlah')
        ->get();

    return view('dashboard.report.siswa-prodi', compact('rekap', 'tahunSekarang'));
}


public function siswaPerSekolah()
{
    $tahunSekarang = Carbon::now()->year;

    $rekap = DataPeserta::select('nama_sekolah', \DB::raw('COUNT(*) as jumlah'))
        ->whereHas('relasiGelombang', function($q) use ($tahunSekarang) {
            $q->where('tahun', $tahunSekarang);
        })
        ->groupBy('nama_sekolah')
        ->orderByDesc('jumlah')
        ->get();

    return view('dashboard.report.siswa-sekolah', compact('rekap', 'tahunSekarang'));
}

public function exportDetailBiaya()
{
    $fileName = 'data_biaya_psb_' . now()->format('d-m-Y_H-i-s') . '.xlsx';
    return Excel::download(new DetailBiayaExport, $fileName);
}

public function exportAllpsb(Request $request) 
{
    $tahun = $request->get('tahun');
    $idGelombang = $request->get('gelombang_id');
    $search = $request->get('search');
    
    $fileName = 'data_psb';
    
    if ($tahun) {
        $cleanTahun = preg_replace('/[\/\\\\]/', '-', $tahun);
        $fileName .= '_tahun-' . $cleanTahun;
    }
    
    if ($idGelombang) {
        $gelombang = MasterGelombang::find($idGelombang);
        if ($gelombang) {
            $cleanGelombang = preg_replace('/[\/\\\\]/', '-', $gelombang->gelombang);
            $fileName .= '_gelombang-' . $cleanGelombang;
        }
    }
    
    if ($search) {
        $cleanSearch = preg_replace('/[\/\\\\]/', '-', $search);
        $cleanSearch = str_replace(' ', '-', $cleanSearch);
        $fileName .= '_search-' . $cleanSearch;
    }
    
    $fileName .= '_' . now()->format('d-m-Y_H-i-s') . '.xlsx';
    
    return Excel::download(new DataPsbAllExport($tahun, $idGelombang, $search), $fileName);
}

public function exportAll(Request $request)
{
    $tahun = $request->get('tahun');
    $idGelombang = $request->get('gelombang_id');
    $search = $request->get('search');

    $tahunList = MasterGelombang::select('tahun_akademik')
        ->distinct()
        ->orderBy('tahun_akademik', 'desc')
        ->get();

    $gelombangList = collect();
    if ($tahun) {
        $gelombangList = MasterGelombang::where('tahun_akademik', $tahun)
            ->orderBy('gelombang')
            ->get();
    }

    $pesertaQuery = DataPeserta::query();

    if ($tahun) {
        $idsGelByTahun = MasterGelombang::where('tahun_akademik', $tahun)->pluck('id');
        $pesertaQuery->whereIn('id_gelombang', $idsGelByTahun);
    }

    if ($idGelombang) {
        $pesertaQuery->where('id_gelombang', $idGelombang);
    }

    if ($search) {
        $pesertaQuery->where(function ($q) use ($search) {
            $q->where('nama_peserta', 'like', "%$search%")
              ->orWhere('no_pendaftaran', 'like', "%$search%")
              ->orWhere('jalur', 'like', "%$search%")
              ->orWhere('fakultas', 'like', "%$search%")
              ->orWhere('prodi', 'like', "%$search%")
              ->orWhere('gelombang', 'like', "%$search%");
        });
    }

    $pesertas = $pesertaQuery->paginate(10);

    $rows = $pesertas->map(function ($p) {
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
            'Program' => $p->prodi ?? '-',
            'Unit' => $p->fakultas ?? '-',
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
            'Bayar Pendaftaran' => $p->status_paid == 1 ? 'Lunas' : 'Belum Lunas',
            'Bayar Registrasi' => $bayarRegistrasi,
            'Nominal Registrasi' => $masterHarga ? $masterHarga->harga_registrasi : '-',
            'Password Login' => $p->plain_password ?? '-',
        ];
    });

    return view('dashboard.report.export-all', [
        'tahunList' => $tahunList,
        'gelombangList' => $gelombangList,
        'tahun' => $tahun,
        'idGelombang' => $idGelombang,
        'search' => $search,
        'pesertas' => $pesertas,
        'rows' => $rows,
    ]);
}

}
