@extends('dashboard.template')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-4">Edit Gelombang</h1>

    <form action="{{ route('master.gelombang.update', $gelombang->id) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="angkatan_id" class="block text-sm font-medium text-gray-700 mb-2">Gelombang (SIKEU)</label>
            <select name="angkatan_id" id="angkatan_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                <option value="">-- Pilih Gelombang dari DB 56 --</option>
                @foreach ($sikeuAngkatan as $a)
                    <option value="{{ $a->id }}" {{ (string) old('angkatan_id', $gelombang->angkatan_id) === (string) $a->id ? 'selected' : '' }}>
                        {{ $a->nama_angkatan }} ({{ $a->tahun }})
                    </option>
                @endforeach
            </select>
            @error('angkatan_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="angkatan_sis_id" class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan (SIKEU)</label>
            <select name="angkatan_sis_id" id="angkatan_sis_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                <option value="">-- Pilih Tahun Angkatan dari DB 56 --</option>
                @foreach ($sikeuAngkatansis as $a)
                    <option value="{{ $a->id }}" {{ (string) old('angkatan_sis_id', $gelombang->angkatan_sis_id) === (string) $a->id ? 'selected' : '' }}>
                        {{ $a->nama_angkatan }} ({{ $a->tahun }})
                    </option>
                @endforeach
            </select>
            @error('angkatan_sis_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="tahun_akademik" class="block text-sm font-medium text-gray-700 mb-2">Label Tahun Akademik (opsional)</label>
            <select name="tahun_akademik" id="tahun_akademik"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Ikuti nama tahun angkatan SIKEU --</option>
                @foreach ($akademik as $a)
                    <option value="{{ $a->tahun_akademik }}" {{ old('tahun_akademik', $gelombang->tahun_akademik) == $a->tahun_akademik ? 'selected' : '' }}>
                        {{ $a->tahun_akademik }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="start" value="{{ old('start', $gelombang->start->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
            <input type="date" name="end" value="{{ old('end', $gelombang->end->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengumuman</label>
            <input type="date" name="pengumuman" value="{{ old('pengumuman', $gelombang->pengumuman->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('master.gelombang') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm mr-2">
                Batal
            </a>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
