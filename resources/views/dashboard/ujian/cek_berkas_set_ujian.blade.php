@extends('dashboard.template')

@section('content')
<div class="bg-white p-6 shadow rounded-lg">
    <h1 class="text-xl font-semibold mb-4">Data Pendaftar</h1>

    <form method="GET" action="{{ url()->current() }}" class="mb-4 flex flex-wrap gap-2">
        <select name="tahun_akademik" class="px-3 py-2 border rounded-lg" onchange="this.form.submit()">
            <option value="">--Pilih Tahun Akademik--</option>
            @foreach($tahunAkademikList as $th)
                <option value="{{ $th->tahun_akademik }}" {{ request('tahun_akademik') == $th->tahun_akademik ? 'selected' : '' }}>
                    {{ $th->tahun_akademik }}
                </option>
            @endforeach
        </select>

        <select name="gelombang_id" class="px-3 py-2 border rounded-lg" onchange="this.form.submit()">
            <option value="">--Pilih Gelombang--</option>
            @foreach($gelombangList as $gel)
                <option value="{{ $gel->id }}" {{ $idGelombang == $gel->id ? 'selected' : '' }}>
                    Gelombang {{ $gel->gelombang }}
                    ({{ \Carbon\Carbon::parse($gel->start)->format('d M Y') }} -
                     {{ \Carbon\Carbon::parse($gel->end)->format('d M Y') }})
                </option>
            @endforeach
        </select>

        <input type="text" name="search" value="{{ request('search') }}" 
            class="w-64 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Cari nama, VA, Jurusan, Sekolah...">

        <button type="submit" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
    </form>

    <div class="mb-4 flex justify-end">
        <button onclick="openParameterModal()"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
            Set Parameter Ujian
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="border px-3 py-2">#</th>
                    <th class="border px-3 py-2">Nama</th>
                    <th class="border px-3 py-2">No. Pend (VA)</th>
                    <th class="border px-3 py-2">Sekolah</th>
                    <th class="border px-3 py-2">Jurusan</th>
                    <th class="border px-3 py-2">Jalur</th>
                    <th class="border px-3 py-2">Kelengkapan Isian</th>
                    <th class="border px-3 py-2">Upload</th>
                    <th class="border px-3 py-2">Bayar Pendaftaran</th>
                    <th class="border px-3 py-2">Detail</th>
                    <th class="border px-3 py-2">
                        <input type="checkbox" id="selectAll" onchange="toggleAll()">
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginatedPeserta as $i => $peserta)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2">{{ $paginatedPeserta->firstItem() + $i }}</td>
                        <td class="border px-3 py-2">{{ $peserta->nama_peserta }}</td>
                        <td class="border px-3 py-2">{{ $peserta->no_pendaftaran ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $peserta->fakultas ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $peserta->prodi ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $peserta->jalur ?? '-' }}</td>
                        <td class="border px-3 py-2 text-center">
                            @php
                                $isianLengkap = true;
                                foreach($fieldWajib as $field){
                                    if(empty($peserta->$field)){
                                        $isianLengkap = false;
                                        break;
                                    }
                                }
                            @endphp
                            @if($isianLengkap)
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded shadow">Lengkap</span>
                            @else
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded shadow">Tidak Lengkap</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2 text-center">
                            @php
                                $uploadWajib = [
                                    'foto' => 'Photo',
                                    'dokumen_kk' => 'KK',
                                    'dokumen_akte_kelahiran' => 'Akte',
                                ];
                            @endphp
                            <div class="flex flex-wrap justify-center gap-1">
                                @foreach($uploadWajib as $field => $label)
                                    @if(!empty($peserta->$field))
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">{{ $label }}</span>
                                    @else
                                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">{{ $label }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="border px-3 py-2 text-center">
                            @if($peserta->status_paid == 1)
                                <span class="text-green-600 font-bold">✔</span>
                            @else
                                <span class="text-red-600 font-bold">✘</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2">
                            <button onclick="openModal({{ $peserta->id }})"
                                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                Lihat
                            </button>
                        </td>
                        <td class="border px-3 py-2 text-center">
                            <input type="checkbox" class="peserta-checkbox" value="{{ $peserta->id }}">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $paginatedPeserta->withQueryString()->links() }}
    </div>
</div>

<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-3/4 max-h-[90vh] overflow-y-auto rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Detail Peserta</h2>
            <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800 text-xl font-bold">✖</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<div id="parameterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl p-6 relative">
        <div class="flex justify-between items-center border-b pb-3 mb-5">
            <h2 class="text-xl font-semibold text-gray-800">Set Parameter Ujian</h2>
            <button onclick="closeParameterModal()" class="text-gray-500 hover:text-red-600 text-2xl font-bold">✖</button>
        </div>
        <form id="parameterForm">
            @csrf
            <div class="space-y-6">
                @foreach($masterUjianList as $ujian)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $ujian->nama }}</label>
                        <div class="flex gap-3">
                            <input type="datetime-local" name="ujian[{{ $ujian->id }}][tanggal]"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <select name="ujian[{{ $ujian->id }}][ruang]" 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">--Pilih Ruang--</option>
                                @foreach($masterRuangList as $r)
                                    <option value="{{ $r->ruang }}">{{ $r->ruang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" onclick="closeParameterModal()" 
                    class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <button type="submit" 
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    fetch(`/api/peserta/${id}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
        });
}
function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}
function openParameterModal() {
    const now = new Date();
    const currentDateTime = now.toISOString().slice(0, 16);
    document.querySelectorAll('#parameterModal input[type="datetime-local"]').forEach(input => {
        input.value = currentDateTime;
    });
    document.getElementById('parameterModal').classList.remove('hidden');
}
function closeParameterModal() {
    document.getElementById('parameterModal').classList.add('hidden');
}
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.peserta-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('peserta-checkbox')) {
        const checkboxes = document.querySelectorAll('.peserta-checkbox');
        const selectAll = document.getElementById('selectAll');
        const checkedBoxes = document.querySelectorAll('.peserta-checkbox:checked');
        selectAll.checked = checkboxes.length === checkedBoxes.length;
    }
});
document.getElementById('parameterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const checkedBoxes = document.querySelectorAll('.peserta-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Silakan pilih minimal satu peserta');
        return;
    }
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
    const ujianData = {};
    document.querySelectorAll('#parameterForm div').forEach(div => {
        const datetime = div.querySelector('input[type="datetime-local"]');
        const ruang = div.querySelector('select[name*="[ruang]"]');
        if (datetime && ruang) {
            const id = datetime.name.match(/\d+/)[0];
            ujianData[id] = {
                tanggal: datetime.value,
                ruang: ruang.value
            };
        }
    });
    const parameterData = {
        peserta_ids: selectedIds,
        ujian: ujianData
    };
    fetch("{{ url('PmbMstPendaftarans/Set-Ujian') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(parameterData)
    })
    .then(res => res.json())
    .then(data => {
        alert('Parameter ujian berhasil disimpan!');
        closeParameterModal();
    })
    .catch(err => {
        alert('Terjadi kesalahan: ' + err.message);
    });
});
window.addEventListener('click', function(e) {
    const detailModal = document.getElementById('detailModal');
    const parameterModal = document.getElementById('parameterModal');
    if (e.target === detailModal) {
        closeModal();
    }
    if (e.target === parameterModal) {
        closeParameterModal();
    }
});
</script>
@endsection
