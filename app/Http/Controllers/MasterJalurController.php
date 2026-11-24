<?php
namespace App\Http\Controllers;

use App\Models\MasterJalur;
use App\Models\MasterHarga;
use Illuminate\Http\Request;

class MasterJalurController extends Controller
{


    public function index()
    {
        $jalurs = MasterJalur::paginate(10);
        return view('dashboard.master-data.jalur.index', compact('jalurs'));
    }

    public function create()
    {
        return view('dashboard.master-data.jalur.create');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'nama' => 'required|string|max:100|unique:jenis_siswa,nama_jenis',
    ]);

    $data['is_active'] = $request->has('is_active');
    $data['is_free']   = $request->has('is_free');

    MasterJalur::create($data);

    return redirect()->route('master.jalur')->with('success','Jalur berhasil ditambahkan');
}


    public function edit($id)
    {
        $masterJalur = MasterJalur::findOrFail($id);
        return view('dashboard.master-data.jalur.edit', compact('masterJalur'));
    }

    public function update(Request $request, $id)
    {
        $masterJalur = MasterJalur::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:jenis_siswa,nama_jenis,' . $id,
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_free']   = $request->boolean('is_free');

        $masterJalur->update($data);

        return redirect()->route('master.jalur')->with('success','Jalur berhasil diupdate');
    }

  public function destroy($id)
{
    $jalur = MasterJalur::findOrFail($id);

    $dipakai = MasterHarga::where('id_jalur', $jalur->id)->exists();
    if ($dipakai) {
        return redirect()->route('master.jalur')
            ->with('error', 'Jalur tidak bisa dihapus karena sudah digunakan di Master Harga');
    }

    $jalur->delete();

    return redirect()->route('master.jalur')->with('success','Jalur berhasil dihapus');
}

}
