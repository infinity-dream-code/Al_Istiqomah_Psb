<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterGelombang;
use App\Models\MasterAkademik;
use App\Models\SikeuAngkatan;
use App\Models\SikeuAngkatansis;

class MasterGelombangController extends Controller
{
    public function index()
    {
        $gelombang = MasterGelombang::with(['akademik', 'sikeuAngkatan', 'sikeuAngkatansis'])->paginate(10);
        return view('dashboard.master-data.gelombang.index', compact('gelombang'));
    }

    public function index2()
    {
        $gelombangs = MasterGelombang::with('akademik')->paginate(10);
        return view('dashboard.settings.gelombang.index', compact('gelombangs'));
    }

    public function create()
    {
        $akademik = MasterAkademik::all();
        $sikeuAngkatan = SikeuAngkatan::orderBy('tahun')->orderBy('nama_angkatan')->get();
        $sikeuAngkatansis = SikeuAngkatansis::orderByDesc('tahun')->orderBy('nama_angkatan')->get();

        return view('dashboard.master-data.gelombang.create', compact(
            'akademik',
            'sikeuAngkatan',
            'sikeuAngkatansis'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'angkatan_id'     => 'required|integer',
            'angkatan_sis_id' => 'required|integer',
            'tahun_akademik'  => 'nullable|string',
            'start'           => 'required|date',
            'end'             => 'required|date|after_or_equal:start',
            'pengumuman'      => 'required|date|after_or_equal:end',
        ]);

        $angkatan = SikeuAngkatan::find($request->angkatan_id);
        $angkatansis = SikeuAngkatansis::find($request->angkatan_sis_id);

        if (!$angkatan || !$angkatansis) {
            return redirect()->back()->withErrors([
                'angkatan_id' => 'Gelombang / tahun angkatan SIKEU tidak ditemukan. Pastikan data ada di DB 56.',
            ])->withInput();
        }

        $gelombangNo = 1;
        if (preg_match('/(\d+)/', (string) $angkatan->nama_angkatan, $m)) {
            $gelombangNo = (int) $m[1];
        }

        $tahun = (int) ($angkatansis->tahun ?: date('Y', strtotime($request->start)));
        $tahunAkademik = $request->tahun_akademik
            ?: (string) $angkatansis->nama_angkatan;

        if (MasterGelombang::where('angkatan_id', $angkatan->id)
            ->where('angkatan_sis_id', $angkatansis->id)
            ->exists()) {
            return redirect()->back()->withErrors([
                'angkatan_id' => 'Kombinasi gelombang & tahun angkatan ini sudah ada.',
            ])->withInput();
        }

        MasterGelombang::create([
            'tahun'           => $tahun,
            'gelombang'       => $gelombangNo,
            'tahun_akademik'  => $tahunAkademik,
            'angkatan_id'     => $angkatan->id,
            'angkatan_sis_id' => $angkatansis->id,
            'start'           => $request->start,
            'end'             => $request->end,
            'pengumuman'      => $request->pengumuman,
            'is_active'       => 1,
        ]);

        return redirect()->route('master.gelombang')
            ->with('success', "Gelombang {$angkatan->nama_angkatan} / {$angkatansis->nama_angkatan} berhasil ditambahkan");
    }

    public function edit($id)
    {
        $gelombang = MasterGelombang::findOrFail($id);
        $akademik = MasterAkademik::all();
        $sikeuAngkatan = SikeuAngkatan::orderBy('tahun')->orderBy('nama_angkatan')->get();
        $sikeuAngkatansis = SikeuAngkatansis::orderByDesc('tahun')->orderBy('nama_angkatan')->get();

        return view('dashboard.master-data.gelombang.edit', compact(
            'gelombang',
            'akademik',
            'sikeuAngkatan',
            'sikeuAngkatansis'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'angkatan_id'     => 'required|integer',
            'angkatan_sis_id' => 'required|integer',
            'tahun_akademik'  => 'nullable|string',
            'start'           => 'required|date',
            'end'             => 'required|date|after_or_equal:start',
            'pengumuman'      => 'required|date|after_or_equal:end',
        ]);

        $gelombang = MasterGelombang::findOrFail($id);
        $angkatan = SikeuAngkatan::find($request->angkatan_id);
        $angkatansis = SikeuAngkatansis::find($request->angkatan_sis_id);

        if (!$angkatan || !$angkatansis) {
            return redirect()->back()->withErrors([
                'angkatan_id' => 'Gelombang / tahun angkatan SIKEU tidak ditemukan. Pastikan data ada di DB 56.',
            ])->withInput();
        }

        $gelombangNo = 1;
        if (preg_match('/(\d+)/', (string) $angkatan->nama_angkatan, $m)) {
            $gelombangNo = (int) $m[1];
        }

        $tahun = (int) ($angkatansis->tahun ?: $gelombang->tahun);
        $tahunAkademik = $request->tahun_akademik
            ?: (string) $angkatansis->nama_angkatan;

        $dup = MasterGelombang::where('angkatan_id', $angkatan->id)
            ->where('angkatan_sis_id', $angkatansis->id)
            ->where('id', '!=', $gelombang->id)
            ->exists();
        if ($dup) {
            return redirect()->back()->withErrors([
                'angkatan_id' => 'Kombinasi gelombang & tahun angkatan ini sudah dipakai baris lain.',
            ])->withInput();
        }

        $gelombang->update([
            'tahun'           => $tahun,
            'gelombang'       => $gelombangNo,
            'tahun_akademik'  => $tahunAkademik,
            'angkatan_id'     => $angkatan->id,
            'angkatan_sis_id' => $angkatansis->id,
            'start'           => $request->start,
            'end'             => $request->end,
            'pengumuman'      => $request->pengumuman,
        ]);

        return redirect()->route('master.gelombang')
            ->with('success', "Gelombang ke-{$gelombang->gelombang} berhasil diperbarui");
    }

    public function destroy($id)
    {
        $gelombang = MasterGelombang::findOrFail($id);

        if ($gelombang->harga()->exists()) {
            return redirect()->route('master.gelombang')
                ->with('error', 'Gelombang tidak bisa dihapus karena sudah dipakai di Master Harga.');
        }

        $gelombang->delete();

        return redirect()->route('master.gelombang')
            ->with('success', 'Gelombang berhasil dihapus.');
    }
}
