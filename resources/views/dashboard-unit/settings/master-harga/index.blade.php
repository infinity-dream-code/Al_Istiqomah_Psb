@extends('dashboard-unit.template')

@section('content')
<div id="master-harga" class="tab-content opacity-100 p-4">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-700">Master Harga SPMB</h3>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full text-sm text-left text-gray-600">
            <thead class="bg-green-600 text-white uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nama Jalur</th>
                    <th class="px-4 py-3">Gelombang</th>
                    <th class="px-4 py-3">Sekolah</th>
                    <th class="px-4 py-3">Jurusan</th>
                    <th class="px-4 py-3">Biaya Registrasi</th>
                    <th class="px-4 py-3">Biaya Daftar Ulang</th>
                    <th class="px-4 py-3 text-center">Aktif</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($masterHarga as $index => $mh)
                <tr class="hover:bg-gray-50 transition" data-jalur="{{ $mh->nama_jalur }}" data-prodi="{{ $mh->nama_prodi }}">
                    <td class="px-4 py-3">{{ $index+1 }}</td>
                    <td class="px-4 py-3 font-medium">{{ $mh->nama_jalur }}</td>
                    <td class="px-4 py-3">Gelombang {{ $mh->nama_gelombang }}</td>
                    <td class="px-4 py-3">{{ $mh->nama_fakultas }}</td>
                    <td class="px-4 py-3">{{ $mh->nama_prodi }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($mh->harga_final, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($mh->harga_registrasi, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer toggle-harga" data-id="{{ $mh->id }}" {{ $mh->active ? 'checked' : '' }} onchange="toggleHargaActive({{ $mh->id }})">
                            <div class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-6 peer-checked:after:border-white"></div>
                        </label>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ url('PmbRefMasterHargaPendaftarans/' . str_replace(' ', '-', $role ?? 'admin') . '/edit/' . $mh->id) }}" 
                           class="text-blue-600 hover:text-blue-800 transition">
                            <i class="fas fa-edit text-lg"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">Belum ada data Master Harga</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleHargaActive(id) {
    const currentCheckbox = document.querySelector(`.toggle-harga[data-id="${id}"]`);
    const currentRow = currentCheckbox.closest('tr');
    const currentJalur = currentRow.getAttribute('data-jalur');
    const currentProdi = currentRow.getAttribute('data-prodi');
    const isActive = currentCheckbox.checked;

    fetch(`/master-harga/toggle-active/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            document.querySelectorAll('tr[data-jalur][data-prodi]').forEach(row => {
                if (row.getAttribute('data-jalur') === currentJalur && row.getAttribute('data-prodi') === currentProdi) {
                    const checkbox = row.querySelector('.toggle-harga');
                    if (checkbox) checkbox.checked = isActive;
                }
            });
        } else {
            alert("Update gagal!");
            currentCheckbox.checked = !isActive;
        }
    })
    .catch(() => {
        alert("Update gagal!");
        currentCheckbox.checked = !isActive;
    });
}
</script>
@endsection
