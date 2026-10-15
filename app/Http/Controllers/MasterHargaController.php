<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterHarga;
use App\Models\Temporary;
use App\Models\MasterRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\MasterJalur;
use App\Models\MasterGelombang;
use App\Models\MasterFakultas;
use App\Models\MasterProdi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MasterHargaController extends Controller
{

     public function index()
    {
        $masterHarga = MasterHarga::with(['jalur', 'gelombang', 'fakultas', 'prodi'])
            ->orderBy('id')
            ->get();

        return view('dashboard.settings.master-harga.index', compact('masterHarga'));
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

    $query = MasterHarga::with(['jalur', 'gelombang', 'fakultas', 'prodi']);

    if (!empty($prodiIds)) {
        $query->whereIn('id_prodi', $prodiIds);
    }

    $masterHarga = $query->orderBy('id')->get();

    return view('dashboard-unit.settings.master-harga.index', compact('masterHarga', 'menus', 'role'));
}

    
public function create(Request $request)
{
    $jalurs = MasterJalur::where('is_active', 1)->orderBy('nama')->get();
    $gelombangs = MasterGelombang::whereDate('end', '>=', Carbon::today())
        ->orderBy('tahun')
        ->orderBy('gelombang')
        ->get();
    $fakultas = MasterFakultas::where('aktif', 1)->orderBy('fakultas')->get();

    $akuns = [];
    try {
        $client = new Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {}

    $temporary = null;
    if ($request->filled(['id_jalur','id_fakultas','id_prodi'])) {
        $temporary = Temporary::where('id_jalur', $request->id_jalur)
            ->where('id_fakultas', $request->id_fakultas)
            ->where('id_prodi', $request->id_prodi)
            ->first();
    }

    return view('dashboard.settings.master-harga.tambah',
        compact('jalurs','gelombangs','fakultas','akuns','temporary'));
}


public function getTemporaryDetail($idJalur, $idFakultas, $idProdi)
{
    $prodi = MasterFakultas::whereKey($idFakultas)->exists()
        ? MasterProdi::resolveFor((int) $idFakultas, $idProdi)
        : null;

    $temporary = $prodi ? Temporary::where('id_jalur', $idJalur)
        ->where('id_fakultas', $idFakultas)
        ->where('id_prodi', $prodi->id)
        ->first() : null;

    if ($temporary) {
        return response()->json([
            'biaya_pendaftaran' => $temporary->biaya_pendaftaran,
            'biaya_registrasi' => $temporary->biaya_registrasi,
            'detail' => $temporary->detail ?? [],
        ]);
    }

    return response()->json([
        'biaya_pendaftaran' => null,
        'biaya_registrasi' => null,
        'detail' => [],
    ]);
}


    public function getProdiByFakultas($fakultasId)
    {
        $prodis = MasterProdi::where('id_fakultas', $fakultasId)
            ->orderBy('nama')
            ->get(['id','nama']);
        return response()->json($prodis);
    }

public function edit($id)
{
    $masterHarga = MasterHarga::findOrFail($id);

    $jalurs = MasterJalur::where('is_active', 1)
        ->orWhere('id', $masterHarga->id_jalur)
        ->orderBy('nama')
        ->get();

    $today = now()->toDateString();
    $gelombangs = MasterGelombang::whereDate('end', '>=', $today)
        ->orWhere('id', $masterHarga->id_gelombang)
        ->orderBy('tahun')
        ->orderBy('gelombang')
        ->get();

    $fakultas = MasterFakultas::where('aktif', 1)
        ->orWhere('id', $masterHarga->id_fakultas)
        ->orderBy('fakultas')
        ->get();

    $akuns = [];
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {}

    return view('dashboard.settings.master-harga.edit', compact(
        'masterHarga',
        'jalurs',
        'gelombangs',
        'fakultas',
        'akuns'
    ));
}




public function update(Request $request, $id)
{
    $request->validate([
        'id_jalur' => 'required',
        'id_gelombang' => 'required',
        'id_fakultas' => 'required',
        'id_prodi' => 'nullable',
        'harga_final' => 'required'
    ]);

    $prodiId = MasterProdi::resolveFor(MasterFakultas::findOrFail($request->id_fakultas)->id, $request->id_prodi)->id;

    $exists = MasterHarga::where('id_jalur', $request->id_jalur)
        ->where('id_gelombang', $request->id_gelombang)
        ->where('id_fakultas', $request->id_fakultas)
        ->where('id_prodi', $prodiId)
        ->where('id', '!=', $id)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Data dengan kombinasi Jalur, Gelombang, Fakultas, dan Prodi tersebut sudah ada di Master Harga. Silakan periksa kembali.');
    }

    $masterHarga = MasterHarga::findOrFail($id);

    $jalur = MasterJalur::findOrFail($request->id_jalur);
    $gelombang = MasterGelombang::findOrFail($request->id_gelombang);
    $fakultas = MasterFakultas::findOrFail($request->id_fakultas);
    $prodi = MasterProdi::resolveFor($fakultas->id, $request->id_prodi);

    $hargaFinal = (int) preg_replace('/[^0-9]/', '', $request->harga_final);

    $akuns = [];
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {}

    $akunMap = [];
    foreach ($akuns as $akun) {
        $akunMap[$akun['KodeAkun']] = $akun['NamaAkun'];
    }

    $detail = [];
    $hargaRegistrasi = 0;
    if ($request->has('detail') && is_array($request->detail)) {
        foreach ($request->detail as $row) {
            if (!empty($row['kode']) && !empty($row['harga'])) {
                $kode = $row['kode'];
                $biaya = (int) preg_replace('/[^0-9]/', '', $row['harga']);
                $detail[] = [
                    'no_akun' => $kode,
                    'nama_tagihan' => $akunMap[$kode] ?? '-',
                    'biaya' => $biaya,
                ];
                $hargaRegistrasi += $biaya;
            }
        }
    }

    $masterHarga->update([
        'id_jalur' => $jalur->id,
        'id_gelombang' => $gelombang->id,
        'id_fakultas' => $fakultas->id,
        'id_prodi' => $prodi->id,
        'harga_final' => $hargaFinal,
        'harga_registrasi' => $hargaRegistrasi,
        'detail' => $detail,
        'nama_jalur' => $jalur->nama,
        'nama_gelombang' => $gelombang->gelombang,
        'nama_fakultas' => $fakultas->fakultas,
        'nama_prodi' => $prodi->nama,
    ]);

    Temporary::updateOrCreate(
        [
            'id_jalur' => $jalur->id,
            'id_fakultas' => $fakultas->id,
            'id_prodi' => $prodi->id,
        ],
        [
            'biaya_pendaftaran' => $hargaFinal,
            'biaya_registrasi' => $hargaRegistrasi,
            'detail' => $detail,
        ]
    );

    return redirect('PmbMstPendaftarans/setting-harga')->with('success', 'Data berhasil diperbarui');
}

public function edit1($role, $id)
{
    $user = Auth::user();
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

    $masterHarga = MasterHarga::findOrFail($id);

    $jalurs = MasterJalur::where('is_active', 1)
        ->orWhere('id', $masterHarga->id_jalur)
        ->orderBy('nama')
        ->get();

    $today = now()->toDateString();
    $gelombangs = MasterGelombang::whereDate('end', '>=', $today)
        ->orWhere('id', $masterHarga->id_gelombang)
        ->orderBy('tahun')
        ->orderBy('gelombang')
        ->get();

    $fakultas = MasterFakultas::where('aktif', 1)
        ->orWhere('id', $masterHarga->id_fakultas)
        ->orderBy('fakultas')
        ->get();

    $akuns = [];
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {}

    return view('dashboard-unit.settings.master-harga.edit', compact(
        'masterHarga', 'jalurs', 'gelombangs', 'fakultas', 'akuns', 'menus', 'role'
    ));
}



public function update1(Request $request, $role, $id)
{
    $request->validate([
        'id_jalur' => 'required',
        'id_gelombang' => 'required',
        'id_fakultas' => 'required',
        'id_prodi' => 'nullable',
        'harga_final' => 'required'
    ]);

    $prodiId = MasterProdi::resolveFor(MasterFakultas::findOrFail($request->id_fakultas)->id, $request->id_prodi)->id;

    $exists = MasterHarga::where('id_jalur', $request->id_jalur)
        ->where('id_gelombang', $request->id_gelombang)
        ->where('id_fakultas', $request->id_fakultas)
        ->where('id_prodi', $prodiId)
        ->where('id', '!=', $id)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Data dengan kombinasi Jalur, Gelombang, Fakultas, dan Prodi tersebut sudah ada di Master Harga. Silakan periksa kembali.');
    }

    $masterHarga = MasterHarga::findOrFail($id);

    $jalur = MasterJalur::findOrFail($request->id_jalur);
    $gelombang = MasterGelombang::findOrFail($request->id_gelombang);
    $fakultas = MasterFakultas::findOrFail($request->id_fakultas);
    $prodi = MasterProdi::resolveFor($fakultas->id, $request->id_prodi);

    $hargaFinal = (int) preg_replace('/[^0-9]/', '', $request->harga_final);

    $akuns = [];
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {}

    $akunMap = [];
    foreach ($akuns as $akun) {
        $akunMap[$akun['KodeAkun']] = $akun['NamaAkun'];
    }

    $detail = [];
    $hargaRegistrasi = 0;
    if ($request->has('detail') && is_array($request->detail)) {
        foreach ($request->detail as $row) {
            if (!empty($row['kode']) && !empty($row['harga'])) {
                $kode = $row['kode'];
                $biaya = (int) preg_replace('/[^0-9]/', '', $row['harga']);
                $detail[] = [
                    'no_akun' => $kode,
                    'nama_tagihan' => $akunMap[$kode] ?? '-',
                    'biaya' => $biaya,
                ];
                $hargaRegistrasi += $biaya;
            }
        }
    }

    $masterHarga->update([
        'id_jalur' => $jalur->id,
        'id_gelombang' => $gelombang->id,
        'id_fakultas' => $fakultas->id,
        'id_prodi' => $prodi->id,
        'harga_final' => $hargaFinal,
        'harga_registrasi' => $hargaRegistrasi,
        'detail' => $detail,
        'nama_jalur' => $jalur->nama,
        'nama_gelombang' => $gelombang->gelombang,
        'nama_fakultas' => $fakultas->fakultas,
        'nama_prodi' => $prodi->nama,
    ]);

    Temporary::updateOrCreate(
        [
            'id_jalur' => $jalur->id,
            'id_fakultas' => $fakultas->id,
            'id_prodi' => $prodi->id,
        ],
        [
            'biaya_pendaftaran' => $hargaFinal,
            'biaya_registrasi' => $hargaRegistrasi,
            'detail' => $detail,
        ]
    );

    return redirect('PmbMstPendaftarans/setting-harga/' . str_replace(' ', '-', $role))
        ->with('success', 'Data berhasil diperbarui');
}



public function toggleActive($id)
{
    try {
        $harga = MasterHarga::findOrFail($id);

        $status = !$harga->active;

        MasterHarga::where('id_jalur', $harga->id_jalur)
            ->where('id_prodi', $harga->id_prodi)
            ->update(['active' => $status]);

        Log::info('Toggle MasterHarga', [
            'id' => $id,
            'id_jalur' => $harga->id_jalur,
            'id_prodi' => $harga->id_prodi,
            'new_status' => $status
        ]);

        return response()->json([
            'success' => true,
            'status'  => $status
        ]);
    } catch (\Exception $e) {
        Log::error('Toggle MasterHarga gagal', [
            'id' => $id,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}



public function store(Request $request)
{
    $request->validate([
        'id_jalur' => 'required',
        'id_gelombang' => 'required',
        'id_fakultas' => 'required',
        'id_prodi' => 'nullable',
        'harga_final' => 'required'
    ]);

    $jalur = MasterJalur::findOrFail($request->id_jalur);
    $gelombang = MasterGelombang::findOrFail($request->id_gelombang);
    $fakultas = MasterFakultas::findOrFail($request->id_fakultas);
    $prodi = MasterProdi::resolveFor($fakultas->id, $request->id_prodi);

    $hargaFinal = preg_replace('/[^0-9]/', '', $request->harga_final);

    $akuns = [];
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('10.99.23.111/WS_PSB/WS_PSB_MASTER/index.php', [
            'json' => [
                'method' => 'getUAkun',
                'token'  => '53c2f9aace5478a11815c65fcdb1a3dc29b60c3e102489384e3c1701f4355fa4'
            ],
            'timeout' => 10
        ]);
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['data']) && is_array($body['data'])) {
            $akuns = $body['data'];
        }
    } catch (\Exception $e) {
    }

    $akunMap = [];
    foreach ($akuns as $akun) {
        $akunMap[$akun['KodeAkun']] = $akun['NamaAkun'];
    }

    $detail = [];
    $hargaRegistrasi = 0;
    if ($request->has('detail') && is_array($request->detail)) {
        foreach ($request->detail as $row) {
            if (!empty($row['kode']) && !empty($row['harga'])) {
                $kode = $row['kode'];
                $biaya = preg_replace('/[^0-9]/', '', $row['harga']);
                $detail[] = [
                    'no_akun' => $kode,
                    'nama_tagihan' => $akunMap[$kode] ?? '-',
                    'biaya' => $biaya,
                ];
                $hargaRegistrasi += (int) $biaya;
            }
        }
    }

    MasterHarga::updateOrCreate(
        [
            'id_jalur' => $jalur->id,
            'id_gelombang' => $gelombang->id,
            'id_fakultas' => $fakultas->id,
            'id_prodi' => $prodi->id,
        ],
        [
            'harga_final' => $hargaFinal,
            'harga_registrasi' => $hargaRegistrasi,
            'detail' => $detail,
            'nama_jalur' => $jalur->nama,
            'nama_gelombang' => $gelombang->gelombang,
            'nama_fakultas' => $fakultas->fakultas,
            'nama_prodi' => $prodi->nama,
            'active' => 1,
        ]
    );

    Temporary::updateOrCreate(
        [
            'id_jalur' => $jalur->id,
            'id_fakultas' => $fakultas->id,
            'id_prodi' => $prodi->id,
        ],
        [
            'biaya_pendaftaran' => $hargaFinal,
            'biaya_registrasi' => $hargaRegistrasi,
            'detail' => $detail,
        ]
    );

    return redirect('PmbMstPendaftarans/setting-harga')->with('success', 'Data berhasil disimpan');
}

}
