@extends('dashboard.template')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800">Master Gelombang</h1>
        <a href="{{ route('master.gelombang.add') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
            + Tambah Gelombang
        </a>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Gelombang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tahun Akademik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tahun Angkatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Start</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">End</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pengumuman</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($gelombang as $index => $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $loop->iteration + ($gelombang->currentPage() - 1) * $gelombang->perPage() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->gelombang }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->tahun_akademik }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->angkatanSis?->nama_angkatan ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->start)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->end)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->pengumuman)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 text-sm">
    <a href="{{ route('master.gelombang.edit', $item->id) }}" 
       class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
    <form action="{{ route('master.gelombang.delete', $item->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Yakin ingin menghapus gelombang ini?')" 
                class="text-red-600 hover:text-red-800">Hapus</button>
    </form>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data gelombang</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $gelombang->links() }}
    </div>
</div>
@endsection
