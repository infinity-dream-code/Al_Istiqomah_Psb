@extends('dashboard-unit.template')
@section('content')

<div class="bg-white p-6 shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Report SPMB - All</h1>

    <div class="flex justify-between items-center mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-3">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Tahun Akademik</label>
                <select name="tahun" id="tahun" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t->tahun_akademik }}" {{ $tahun == $t->tahun_akademik ? 'selected' : '' }}>
                            {{ $t->tahun_akademik }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Gelombang</label>
                <select name="gelombang_id" id="gelombang" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" {{ !$tahun ? 'disabled' : '' }}>
                    <option value="">-- Semua Gelombang --</option>
                    @if($tahun)
                        @foreach($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ $idGelombang == $g->id ? 'selected' : '' }}>
                                Gelombang {{ $g->gelombang }} - {{ $g->tahun_akademik }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-end gap-2">
                <input type="text" name="search" placeholder="Cari..." value="{{ $search }}" class="px-3 py-2 border rounded-lg">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Filter</button>
            </div>
        </form>

        <div>
           <a href="{{ route('report.exportAllpsb1', array_merge(['role' => str_replace(' ', '-', $role ?? 'admin')], request()->all())) }}" 
   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    Export
</a>

        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">#</th>
                    @if($rows->isNotEmpty())
                        @foreach(array_keys($rows->first()) as $col)
                            <th class="border px-2 py-1">{{ $col }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $r)
                    <tr class="odd:bg-white even:bg-gray-50">
                        <td class="border px-2 py-1 text-center">{{ $pesertas->firstItem() + $i }}</td>
                        @foreach($r as $val)
                            <td class="border px-2 py-1">{{ $val }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="99" class="text-center py-4">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesertas->hasPages())
        <div class="mt-4">
            {{ $pesertas->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
document.getElementById('tahun').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('gelombang').addEventListener('change', function() {
    this.form.submit();
});
</script>

@endsection