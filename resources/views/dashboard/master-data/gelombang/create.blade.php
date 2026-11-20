@extends('dashboard.template')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-4">Tambah Gelombang</h1>

    <div class="mb-4 p-4 rounded-lg bg-yellow-100 border border-yellow-400 text-yellow-800 text-sm">
        Setiap tahun maksimal hanya boleh <strong>10 gelombang</strong>.
    </div>

    <form action="{{ route('master.gelombang.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf

        <div class="mb-4">
            <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-2">Tahun Akademik</label>
            <select name="tahun_akademik" id="tahun_akademik"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-sm"
                    required>
                <option value="">-- Pilih Tahun Akademik --</option>
                @foreach ($akademik as $a)
                    <option value="{{ $a->tahun_akademik }}" {{ old('tahun_akademik') == $a->tahun_akademik ? 'selected' : '' }}>
                        {{ $a->tahun_akademik }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="angkatan_sis_id" class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan (SIKEU)</label>
            <select name="angkatan_sis_id" id="angkatan_sis_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-sm">
                <option value="">-- Pilih Tahun Angkatan --</option>
                @foreach ($angkatansis as $a)
                    <option value="{{ $a->id }}" {{ (string) old('angkatan_sis_id') === (string) $a->id ? 'selected' : '' }}>
                        {{ $a->nama_angkatan }} ({{ $a->tahun }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Dipakai sebagai <code>siswa.angkatan_sis_id</code>. Nomor gelombang sendiri jadi <code>siswa.angkatan_id</code>.</p>
        </div>

        <div class="mb-4">
            <label for="start" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" id="start" name="start" 
                   value="{{ old('start') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-sm"
                   required>
        </div>

        <div class="mb-4">
            <label for="end" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai <span class="text-gray-500 text-xs">(≥ Mulai)</span></label>
            <input type="date" id="end" name="end" 
                   value="{{ old('end') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-sm"
                   required>
        </div>

        <div class="mb-4">
            <label for="pengumuman" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengumuman <span class="text-gray-500 text-xs">(≥ Selesai)</span></label>
            <input type="date" id="pengumuman" name="pengumuman" 
                   value="{{ old('pengumuman') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-sm"
                   required>
        </div>

        @error('gelombang')
            <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
        @enderror

        <div class="flex justify-end">
            <a href="{{ route('master.gelombang') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm mr-2">
                Batal
            </a>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
