<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeserta;
use Illuminate\Support\Facades\Auth;
use App\Models\Informasi;
use App\Models\MasterGelombang;
use App\Models\MasterRole;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\ImageSlider;
use App\Models\Brosur;

class DashboardController extends Controller
{

public function role($role)
{
    $user = Auth::user();
    $normalizedRole = Str::of($role)->replace('-', ' ')->__toString();
    $roleData = MasterRole::whereRaw('LOWER(nama_role) = ?', [strtolower($normalizedRole)])->first();

    // Fallback: cocokkan tanpa spasi/strip
    if (!$roleData) {
        $compact = strtolower(preg_replace('/[\s\-]+/', '', $normalizedRole));
        $roleData = MasterRole::get()->first(function ($r) use ($compact) {
            return strtolower(preg_replace('/[\s\-]+/', '', (string) $r->nama_role)) === $compact;
        });
    }

    $menus = [];

    $today = now();
    $activeGelombang = MasterGelombang::where('start', '<=', $today)
        ->where('end', '>=', $today)
        ->where('is_active', true)
        ->first();

    if (!$activeGelombang) {
        $activeGelombang = MasterGelombang::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    $prodiIds = [];
    if ($user && $user->prodi_id) {
        $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        $prodiIds = array_values(array_filter(array_map('trim', $prodiIds), fn ($v) => $v !== '' && $v !== null));
    }

    $stats = [
        'total_pendaftar' => 0,
        'total_bayar' => 0,
        'total_lulus' => 0,
        'total_registrasi' => 0,
        'percent_bayar' => 0,
        'percent_lulus' => 0,
        'percent_registrasi' => 0,
    ];

    $monthlyChart = [
        'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        'pendaftar' => array_fill(0, 12, 0),
        'bayar' => array_fill(0, 12, 0),
    ];

    // Jangan query kalau gelombang/prodi belum siap (ini yang sering bikin error setelah login role unit)
    if ($activeGelombang && !empty($prodiIds)) {
        $queryBase = DataPeserta::whereIn('id_prodi', $prodiIds)
            ->where('id_gelombang', $activeGelombang->id);

        $stats['total_pendaftar'] = (clone $queryBase)->count();
        $stats['total_bayar'] = (clone $queryBase)->where('status_paid', 1)->count();
        $stats['total_lulus'] = (clone $queryBase)->where('status_ujian', 'lulus')->count();
        $stats['total_registrasi'] = (clone $queryBase)->where('status_pembayaran_registrasi', 1)->count();

        $stats['percent_bayar'] = $stats['total_pendaftar'] > 0 ? round(($stats['total_bayar'] / $stats['total_pendaftar']) * 100) : 0;
        $stats['percent_lulus'] = $stats['total_pendaftar'] > 0 ? round(($stats['total_lulus'] / $stats['total_pendaftar']) * 100) : 0;
        $stats['percent_registrasi'] = $stats['total_pendaftar'] > 0 ? round(($stats['total_registrasi'] / $stats['total_pendaftar']) * 100) : 0;

        $year = date('Y');
        foreach (range(1, 12) as $m) {
            $monthlyChart['pendaftar'][$m - 1] = DataPeserta::whereIn('id_prodi', $prodiIds)
                ->where('id_gelombang', $activeGelombang->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();

            $monthlyChart['bayar'][$m - 1] = DataPeserta::whereIn('id_prodi', $prodiIds)
                ->where('id_gelombang', $activeGelombang->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->where('status_paid', 1)
                ->count();
        }
    }

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

    // Pastikan $role di view selalu slug aman (bukan nama dengan spasi)
    $role = Str::slug($normalizedRole, '-');

    return view('dashboard-unit.index', compact('menus', 'user', 'role', 'monthlyChart', 'stats', 'activeGelombang'));
}


public function index1()
{
    $today = Carbon::today();
    $tahunIni = Carbon::now()->year;

    $gelombangTahunIni = MasterGelombang::where(function($q) use ($tahunIni) {
            $q->whereYear('start', $tahunIni)
              ->orWhereYear('end', $tahunIni);
        })
        ->whereDate('end', '>=', $today)
        ->orderBy('start', 'asc')
        ->get();

    $sliders = ImageSlider::orderByDesc('tanggal_upload')->get();
    $brosurs = Brosur::orderByDesc('tanggal_upload')->get();
    $informasi = Informasi::with('user')->latest()->get();

    return view('index', compact('gelombangTahunIni', 'sliders', 'brosurs', 'informasi'));
    }



    public function index()
    {
        $today = Carbon::today();

        $activeGelombang = MasterGelombang::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->where('is_active', true)
            ->first();

        if (!$activeGelombang) {
            $activeGelombang = MasterGelombang::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $stats = $this->calculateStats($activeGelombang);
        $year = date('Y');
        $monthlyChart = $this->getMonthlyChart($year);

        return view('dashboard.index', compact('stats', 'activeGelombang', 'monthlyChart'));
    }

  private function calculateStats($gelombang)
{
    if (!$gelombang) {
        return [
            'total_pendaftar' => 0,
            'total_bayar' => 0,
            'total_lulus' => 0,
            'total_registrasi' => 0,
            'percent_bayar' => 0,
            'percent_lulus' => 0,
            'percent_registrasi' => 0
        ];
    }

    $totalPendaftar = DataPeserta::where('id_gelombang', $gelombang->id)
        ->whereYear('created_at', date('Y'))
        ->count();

    $totalBayar = DataPeserta::where('id_gelombang', $gelombang->id)
        ->whereYear('created_at', date('Y'))
        ->where('status_paid', 1)
        ->count();

    $totalLulus = DataPeserta::where('id_gelombang', $gelombang->id)
        ->whereYear('created_at', date('Y'))
        ->where('status_ujian', 'lulus')
        ->count();

    $totalRegistrasi = DataPeserta::where('id_gelombang', $gelombang->id)
        ->whereYear('created_at', date('Y'))
        ->where('status_pembayaran_registrasi', 1)
        ->count();

    $percentBayar = $totalPendaftar > 0 ? round(($totalBayar / $totalPendaftar) * 100) : 0;
    $percentLulus = $totalPendaftar > 0 ? round(($totalLulus / $totalPendaftar) * 100) : 0;
    $percentRegistrasi = $totalPendaftar > 0 ? round(($totalRegistrasi / $totalPendaftar) * 100) : 0;

    return [
        'total_pendaftar' => $totalPendaftar,
        'total_bayar' => $totalBayar,
        'total_lulus' => $totalLulus,
        'total_registrasi' => $totalRegistrasi,
        'percent_bayar' => $percentBayar,
        'percent_lulus' => $percentLulus,
        'percent_registrasi' => $percentRegistrasi
    ];
}


    private function getMonthlyChart($year)
    {
        $pendaftar = DataPeserta::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->pluck('total','bulan');

        $bayar = DataPeserta::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->where('status_paid', 1)
            ->groupBy('bulan')
            ->pluck('total','bulan');

        $labels = [];
        $dataPendaftar = [];
        $dataBayar = [];

        foreach (range(1,12) as $m) {
            $labels[] = date('M', mktime(0,0,0,$m,1));
            $dataPendaftar[] = $pendaftar[$m] ?? 0;
            $dataBayar[] = $bayar[$m] ?? 0;
        }

        return [
            'labels' => $labels,
            'pendaftar' => $dataPendaftar,
            'bayar' => $dataBayar
        ];
    }
}
