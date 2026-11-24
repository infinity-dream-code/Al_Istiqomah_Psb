<?php

namespace App\Http\Controllers;

use App\Models\MasterFakultas;
use App\Models\MasterProdi;
use Illuminate\Http\Request;
use App\Models\MasterHarga;
use App\Models\MasterRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MasterBiayaPendaftaranController extends Controller
{
    
     public function index()
    {
        $unit = MasterFakultas::paginate(10);
        return view('dashboard.master-data.unit.index', compact('unit'));
    }

      public function index2()
    {
        $fakultas = MasterFakultas::orderBy('id')->get();
        return view('dashboard.settings.pendaftar.index', compact('fakultas'));
    }

    public function index3(Request $request)
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

    $fakultas = collect(); 
    if (!empty($prodiIds)) {
        $fakultasIds = MasterProdi::whereIn('id', $prodiIds)
            ->pluck('id_fakultas')
            ->unique()
            ->toArray();

        $fakultas = MasterFakultas::whereIn('id', $fakultasIds)
            ->orderBy('id')
            ->get();
    }

    return view('dashboard-unit.settings.pendaftar.index', compact('fakultas', 'menus', 'role'));
}


    public function create()
    {
        return view('dashboard.master-data.unit.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'fakultas' => 'required|string|max:255',
    ]);

    MasterFakultas::create([
        'fakultas' => $request->fakultas,
    ]);

    return redirect()->route('master.unit')->with('success', 'Fakultas berhasil ditambahkan.');
}


   public function destroy($id)
{
    $unit = MasterFakultas::findOrFail($id);

    $dipakai = MasterHarga::where('id_fakultas', $unit->id)->exists();
    if ($dipakai) {
        return redirect()->route('master.unit')
            ->with('error', 'Unit tidak bisa dihapus karena sudah digunakan di Master Harga.');
    }

    $unit->delete();

    return redirect()->route('master.unit')
        ->with('success', 'Fakultas berhasil dihapus.');
}


  
public function edit($id)
{
    $biaya = MasterFakultas::findOrFail($id);
    return view('dashboard.master-data.unit.edit', compact('biaya'));
}
   
public function update(Request $request, $id)
{
    $biaya = MasterFakultas::findOrFail($id);

    $data = $request->validate([
        'fakultas' => 'required|string|max:150|unique:unit_sekolah,nama_unit,' . $biaya->id,
    ]);

    $data['aktif'] = 1;

    $biaya->update($data);

    return redirect('PmbMstPendaftarans/master-unit')->with('success', 'succes diupdate');
}

   
  
}
