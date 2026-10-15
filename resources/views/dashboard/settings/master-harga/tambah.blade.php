@extends('dashboard.template')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tambah Master Harga</h3>
            <a href="{{ url('PmbMstPendaftarans/setting-harga') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="p-6">
            <form action="{{ route('master-harga.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_jalur" class="block text-sm font-medium text-gray-700 mb-2">Jalur <span class="text-red-500">*</span></label>
                        <select id="id_jalur" name="id_jalur" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="id_gelombang" class="block text-sm font-medium text-gray-700 mb-2">Gelombang <span class="text-red-500">*</span></label>
                        <select id="id_gelombang" name="id_gelombang" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $gelombang)
                                <option value="{{ $gelombang->id }}">Gelombang {{ $gelombang->gelombang }} - {{ $gelombang->tahun_akademik }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="id_fakultas" class="block text-sm font-medium text-gray-700 mb-2">Sekolah <span class="text-red-500">*</span></label>
                        <select id="id_fakultas" name="id_fakultas" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->id }}">{{ $fak->fakultas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="harga_final" class="block text-sm font-medium text-gray-700 mb-2">Biaya Registrasi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" id="harga_final" name="harga_final" placeholder="0" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="text-center font-semibold text-gray-700 text-lg mt-6 mb-2">Biaya Daftar Ulang</div>

                <div id="detail-container" class="space-y-4"></div>
                <button type="button" id="add-detail" class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"><i class="fas fa-plus"></i> Tambah Tagihan Daful</button>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ url('PmbMstPendaftarans/setting-harga') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2"><i class="fas fa-times"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    function formatNumber(value) {
        let number = value.replace(/[^0-9]/g, '');
        if (number === '') return '';
        return new Intl.NumberFormat('id-ID').format(number);
    }

    $('#harga_final, #harga_registrasi').on('input', function() {
        $(this).val(formatNumber($(this).val()));
    });

    $(document).on('input', 'input[name^="detail"]', function() {
        $(this).val(formatNumber($(this).val()));
    });

    $('#id_jalur, #id_fakultas').on('change', function() {
        let jalurId = $('#id_jalur').val();
        let fakultasId = $('#id_fakultas').val();
        if (jalurId && fakultasId) {
            $.get("{{ url('get-temporary-detail') }}/" + jalurId + "/" + fakultasId + "/0", function(data) {
                let container = $('#detail-container');
                container.empty();
                if (data.biaya_pendaftaran) {
                    $('#harga_final').val(new Intl.NumberFormat('id-ID').format(data.biaya_pendaftaran));
                }
                if (data.biaya_registrasi) {
                    $('#harga_registrasi').val(new Intl.NumberFormat('id-ID').format(data.biaya_registrasi));
                }
                if (data.detail.length > 0) {
                    $.each(data.detail, function(i, row) {
                        let html = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 detail-row">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Akun</label>
                                    <select name="detail[${i}][kode]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach($akuns as $akun)
                                            <option value="{{ $akun['KodeAkun'] }}" ${row.no_akun == "{{ $akun['KodeAkun'] }}" ? 'selected' : ''}>
                                                {{ $akun['KodeAkun'] }} - {{ $akun['NamaAkun'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                            <input type="text" name="detail[${i}][harga]" value="${row.biaya}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                    <button type="button" class="remove-detail bg-red-500 text-white px-3 py-2 rounded-lg"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>`;
                        container.append(html);
                    });
                }
            });
        }
    });

    $(document).on('click', '.remove-detail', function() {
        $(this).closest('.detail-row').remove();
    });

    let detailIndex = 1;
    $('#add-detail').on('click', function() {
        let html = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6 detail-row">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Akun</label>
                <select name="detail[${detailIndex}][kode]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Pilih Akun --</option>
                    @foreach($akuns as $akun)
                        <option value="{{ $akun['KodeAkun'] }}">{{ $akun['KodeAkun'] }} - {{ $akun['NamaAkun'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" name="detail[${detailIndex}][harga]" placeholder="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <button type="button" class="remove-detail bg-red-500 text-white px-3 py-2 rounded-lg"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;
        $('#detail-container').append(html);
        detailIndex++;
    });
});
</script>
@endsection
