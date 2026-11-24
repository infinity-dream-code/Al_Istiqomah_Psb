<?php

namespace App\Http\Controllers;

use App\Models\MasterProdi;
use App\Models\MasterHarga;
use App\Models\MasterFakultas;
use Illuminate\Http\Request;
use App\Models\MasterRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MasterProdiController extends Controller
{
    public function index()
    {
        $sekolah = MasterProdi::with('fakultas')->paginate(10);
        return view('dashboard.master-data.sekolah.index', compact('sekolah'));
    }
    
    public function create()
    {
        $fakultas = MasterFakultas::all();
        return view('dashboard.master-data.sekolah.create', compact('fakultas'));
    }


public function store(Request $request)
{

    $request->validate([
        'id_fakultas' => 'required|exists:unit_sekolah,id',
        'nama' => 'required|string|max:255',
    ]);

    MasterProdi::create([
        'id_fakultas' => $request->id_fakultas,
        'nama' => $request->nama
    ]);

    return redirect('PmbMstPendaftarans/master-sekolah')->with('success', 'Program Studi berhasil ditambahkan');
}


    public function edit($id)
{
    $prodi = MasterProdi::with('fakultas')->findOrFail($id);
    $fakultas = MasterFakultas::all(); 
    return view('dashboard.master-data.sekolah.edit', compact('prodi', 'fakultas'));
}


   public function update(Request $request, $id)
{
    $prodi = MasterProdi::findOrFail($id);

    $request->validate([
        'id_fakultas' => 'required|exists:unit_sekolah,id', 
        'nama' => 'required|string|max:255',
    ]);

    $prodi->id_fakultas = $request->id_fakultas;
    $prodi->nama = $request->nama;
    $prodi->save();

    return redirect()->route('master.sekolah')
                     ->with('success', 'Data berhasil diperbarui');
}


    public function destroy($id)
{
    $prodi = MasterProdi::findOrFail($id);

    $dipakai = MasterHarga::where('id_prodi', $prodi->id)->exists();
    if ($dipakai) {
        return redirect()->route('master.sekolah')
            ->with('error', 'Sekolah tidak bisa dihapus karena sudah digunakan di Master Harga');
    }

    $prodi->delete();

    return redirect()->route('master.sekolah')->with('success', 'Data berhasil dihapus');
}

public function index1(Request $request)
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

    $prodiIds = [];
    if ($user->prodi_id) {
        $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
    }

    $query = MasterProdi::with('fakultas');

    if (!empty($prodiIds)) {
        $fakultasIds = MasterProdi::whereIn('id', $prodiIds)
            ->pluck('id_fakultas')
            ->unique()
            ->toArray();

        $query->whereIn('id_fakultas', $fakultasIds);
    }

    $sekolah = $query->paginate(10);

    return view('dashboard-unit.master-data.sekolah.index', compact('sekolah', 'menus', 'role'));
}


    public function create1(Request $request, $role)
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

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        }

        $fakultas = MasterFakultas::whereHas('prodi', function ($q) use ($prodiIds) {
            $q->whereIn('id', $prodiIds);
        })->get();

        return view('dashboard-unit.master-data.sekolah.create', compact('fakultas', 'menus', 'role'));
    }

    public function store1(Request $request, $role)
    {
        $request->validate([
            'id_fakultas' => 'required|exists:unit_sekolah,id',
            'nama' => 'required|string|max:255',
        ]);

        MasterProdi::create([
            'id_fakultas' => $request->id_fakultas,
            'nama' => $request->nama
        ]);

        return redirect('/PmbMstPendaftarans/master-sekolah/' . str_replace(' ', '-', $role))->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function edit1($role, $id)
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

        $prodi = MasterProdi::with('fakultas')->findOrFail($id);

        $prodiIds = [];
        if ($user->prodi_id) {
            $prodiIds = is_array($user->prodi_id) ? $user->prodi_id : explode(',', $user->prodi_id);
        }

        $fakultas = MasterFakultas::whereHas('prodi', function ($q) use ($prodiIds) {
            $q->whereIn('id', $prodiIds);
        })->get();

        return view('dashboard-unit.master-data.sekolah.edit', compact('prodi', 'fakultas', 'menus', 'role'));
    }

    public function update1(Request $request, $role, $id)
    {
        $prodi = MasterProdi::findOrFail($id);

        $request->validate([
            'id_fakultas' => 'required|exists:unit_sekolah,id',
            'nama' => 'required|string|max:255',
        ]);

        $prodi->id_fakultas = $request->id_fakultas;
        $prodi->nama = $request->nama;
        $prodi->save();

        return redirect('/PmbMstPendaftarans/master-sekolah/' . str_replace(' ', '-', $role))->with('success', 'Data berhasil diperbarui');
    }

    public function destroy1($role, $id)
    {
        $prodi = MasterProdi::findOrFail($id);
        $dipakai = MasterHarga::where('id_prodi', $prodi->id)->exists();
        if ($dipakai) {
            return redirect('/PmbMstPendaftarans/master-sekolah/' . str_replace(' ', '-', $role))->with('error', 'Sekolah tidak bisa dihapus karena sudah digunakan di Master Harga');
        }

        $prodi->delete();

        return redirect('/PmbMstPendaftarans/master-sekolah/' . str_replace(' ', '-', $role))->with('success', 'Data berhasil dihapus');
    }

}
