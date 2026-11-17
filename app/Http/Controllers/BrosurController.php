<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brosur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrosurController extends Controller
{
    public function index()
    {
        $brosurs = Brosur::with('user')->orderByDesc('tanggal_upload')->get();
        return view('dashboard.settings.brosur.index', compact('brosurs'));
    }

    public function create()
    {
        return view('dashboard.settings.brosur.create');
    }

public function store(Request $request)
{
    if (Brosur::count() >= 8) {
        return redirect()->route('setting-brosur.index')->with('error', 'Maksimal hanya 8 brosur yang bisa diupload.');
    }

    $request->validate([
        'brosur' => 'required|mimes:pdf,jpg,jpeg,png|max:4096'
    ]);

    $file = $request->file('brosur');
    $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
    $path = $file->storeAs('brosur', Str::random(40) . '.' . $ext, 'public');

    Brosur::create([
        'id_user' => Auth::id(),
        'brosur' => $path,
        'tanggal_upload' => now()
    ]);

    return redirect()->route('setting-brosur.index')->with('success', 'Brosur berhasil ditambahkan');
}



    public function edit(Brosur $setting_brosur)
    {
        return view('dashboard.settings.brosur.edit', compact('setting_brosur'));
    }

public function update(Request $request, Brosur $setting_brosur)
{
    $request->validate([
        'brosur' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048'
    ]);

    if ($request->hasFile('brosur')) {
        if ($setting_brosur->brosur && Storage::disk('public')->exists($setting_brosur->brosur)) {
            Storage::disk('public')->delete($setting_brosur->brosur);
        }

        $file = $request->file('brosur');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $path = $file->storeAs('brosur', Str::random(40) . '.' . $ext, 'public');

        $setting_brosur->brosur = $path;
        $setting_brosur->tanggal_upload = now();
    }

    $setting_brosur->id_user = Auth::id();
    $setting_brosur->save();

    return redirect()->route('setting-brosur.index')->with('success', 'Brosur berhasil diperbarui');
}


    public function destroy(Brosur $setting_brosur)
    {
        if ($setting_brosur->brosur && Storage::disk('public')->exists($setting_brosur->brosur)) {
            Storage::disk('public')->delete($setting_brosur->brosur);
        }

        $setting_brosur->delete();
        return redirect()->route('setting-brosur.index')->with('success', 'Brosur berhasil dihapus');
    }
}
