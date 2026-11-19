@extends('dashboard.template')

@section('content')
<div id="on-off" class="tab-content opacity-100 p-4">
    <h3 class="text-xl font-bold text-gray-700 mb-6">Set On/Off SPMB</h3>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full text-sm text-left text-gray-600">
            <thead class="bg-green-600 text-white uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Fakultas</th>
                    <th class="px-6 py-3 text-center">Aktif / Nonaktifkan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($fakultas as $i => $f)
                <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition">
                    <td class="px-6 py-4">{{ $i+1 }}</td>
                    <td class="px-6 py-4 font-medium">{{ strtoupper($f->fakultas) }}</td>
                    <td class="px-6 py-4 text-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" {{ $f->aktif ? 'checked' : '' }} onchange="toggleFakultas({{ $f->id }})">
                            <div class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-6 peer-checked:after:border-white"></div>
                        </label>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFakultas(id) {
    fetch("{{ secure_url('fakultas/toggle') }}/" + id, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if(!data.success){
            alert("Update gagal!");
        }
    })
    .catch(() => alert("Update gagal!"));
}
</script>
@endsection
