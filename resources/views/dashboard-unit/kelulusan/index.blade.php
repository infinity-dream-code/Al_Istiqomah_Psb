@extends('dashboard-unit.template')

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
            placeholder="Cari nama, VA, prodi, fakultas...">

        <button type="submit" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
    </form>

    <div class="mb-4 flex justify-end gap-3">
        <button onclick="setKelulusan('gagal')"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
            Gagal
        </button>
        <button onclick="openLulusModal()"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
            Lulus
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="border px-3 py-2">#</th>
                    <th class="border px-3 py-2">Nama</th>
                    <th class="border px-3 py-2">No. Pend (VA)</th>
                    <th class="border px-3 py-2">Fakultas</th>
                    <th class="border px-3 py-2">Prodi</th>
                    <th class="border px-3 py-2">Jalur</th>
                    <th class="border px-3 py-2">Kelengkapan Isian</th>
                    <th class="border px-3 py-2">Upload</th>
                    <th class="border px-3 py-2">Bayar Pendaftaran</th>
                    <th class="border px-3 py-2">Status Ujian</th>
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
                    <td class="border px-3 py-2 text-center">
                        @if($peserta->status_ujian === 'lulus')
                            <span class="inline-block bg-green-500 text-white text-sm font-medium px-3 py-1 rounded">
                                Lulus
                            </span>
                        @elseif($peserta->status_ujian === 'gagal')
                            <span class="inline-block bg-red-500 text-white text-sm font-medium px-3 py-1 rounded">
                                Gagal
                            </span>
                        @else
                            <span class="inline-block bg-gray-500 text-white text-sm font-medium px-3 py-1 rounded">
                                Proses
                            </span>
                        @endif
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

<div id="lulusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl p-6 relative">
        <div class="flex justify-between items-center border-b pb-3 mb-5">
            <h2 class="text-xl font-semibold text-gray-800">Set Jadwal Registrasi</h2>
            <button onclick="closeLulusModal()" class="text-gray-500 hover:text-red-600 text-2xl font-bold">✖</button>
        </div>
        <form id="lulusForm">
            @csrf
            <input type="hidden" name="peserta_ids" id="peserta_ids_lulus">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batas Awal Registrasi</label>
                    <input type="datetime-local" name="awal" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batas Akhir Registrasi</label>
                    <input type="datetime-local" name="akhir" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pembekalan</label>
                    <input type="datetime-local" name="pembekalan" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" onclick="closeLulusModal()" 
                    class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg">Close</button>
                <button type="submit" 
                    class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.peserta-checkbox');
        checkboxes.forEach(checkbox => { checkbox.checked = selectAll.checked; });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('peserta-checkbox')) {
            const checkboxes = document.querySelectorAll('.peserta-checkbox');
            const selectAll = document.getElementById('selectAll');
            const checkedBoxes = document.querySelectorAll('.peserta-checkbox:checked');
            selectAll.checked = checkboxes.length === checkedBoxes.length;
        }
    });

    function setKelulusan(status) {
        const checkedPeserta = document.querySelectorAll('.peserta-checkbox:checked');
        if (checkedPeserta.length === 0) {
            alert('Silakan pilih minimal satu peserta');
            return;
        }

        const selectedPesertaIds = Array.from(checkedPeserta).map(cb => cb.value).join(',');
        
        if (status === 'gagal') {
            let url = "{{ url('PmbMstPendaftarans/set_kelulusanku') }}/" + status + "?ids=" + selectedPesertaIds;
            window.location.href = url;
            return;
        }

        if (status === 'lulus') {
            openLulusModal();
        }
    }

    function openLulusModal() {
        const checkedPeserta = document.querySelectorAll('.peserta-checkbox:checked');
        if (checkedPeserta.length === 0) {
            alert('Silakan pilih minimal satu peserta');
            return;
        }

        const selectedPesertaIds = Array.from(checkedPeserta).map(cb => cb.value).join(',');
        
        document.getElementById('peserta_ids_lulus').value = selectedPesertaIds;
        document.getElementById('lulusModal').classList.remove('hidden');
    }

    function closeLulusModal() {
        document.getElementById('lulusModal').classList.add('hidden');
    }

    document.getElementById('lulusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const pesertaIds = formData.get('peserta_ids');
        const awal = formData.get('awal');
        const akhir = formData.get('akhir');
        const pembekalan = formData.get('pembekalan');
        
        let url = "{{ url('PmbMstPendaftarans/set_kelulusanku/lulus') }}?ids=" + pesertaIds;
        url += "&awal=" + encodeURIComponent(awal);
        url += "&akhir=" + encodeURIComponent(akhir);
        if (pembekalan) {
            url += "&pembekalan=" + encodeURIComponent(pembekalan);
        }
        
        window.location.href = url;
    });
</script>
@endsection