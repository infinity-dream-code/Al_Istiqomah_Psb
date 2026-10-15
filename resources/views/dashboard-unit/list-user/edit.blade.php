@extends('dashboard-unit.template')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Edit Data Peserta</h3>
            <a href="{{ url('PmbMstPendaftarans/list-user/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="p-6">
            @php
                $isLulus = (strtoupper($peserta->status_ujian) === 'LULUS');
                $disabledClass = $isLulus ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '';
                $disabledAttr = $isLulus ? 'disabled readonly' : '';
            @endphp

            @if($isLulus)
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg">
                    Peserta ini sudah <strong>LULUS</strong>. Hanya Nama dan Nomor HP yang akan diperbarui, perubahan Jalur, Gelombang, Fakultas, dan Prodi akan diabaikan.
                </div>
            @endif
            
            <form action="{{ url('PmbMstPendaftarans/' . str_replace(' ', '-', $role ?? 'admin') . '/peserta/update/' . $peserta->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Calon Siswa</label>
                        <input type="text" name="nama_peserta" value="{{ $peserta->nama_peserta }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP</label>
                        <input type="text" name="no_hp" value="{{ $peserta->no_hp }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jalur</label>
                        <select name="id_jalur" id="id_jalur" class="w-full px-3 py-2 border border-gray-300 rounded-lg {{ $disabledClass }}" {{ $disabledAttr }}>
                            <option value="">-- Pilih Jalur --</option>
                            @foreach($jalurs as $j)
                                <option value="{{ $j->id_jalur }}" {{ $peserta->id_jalur == $j->id_jalur ? 'selected' : '' }}>
                                    {{ $j->nama_jalur }}
                                </option>
                            @endforeach
                        </select>
                        @if($isLulus)
                            <input type="hidden" name="id_jalur" value="{{ $peserta->id_jalur }}">
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gelombang</label>
                        <select name="id_gelombang" id="id_gelombang" class="w-full px-3 py-2 border border-gray-300 rounded-lg {{ $disabledClass }}" {{ $disabledAttr }}>
                            <option value="">-- Pilih Gelombang --</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g['id_gelombang'] }}" {{ $peserta->id_gelombang == $g['id_gelombang'] ? 'selected' : '' }}>
                                    {{ $g['nama_gelombang'] }}
                                </option>
                            @endforeach
                        </select>
                        @if($isLulus)
                            <input type="hidden" name="id_gelombang" value="{{ $peserta->id_gelombang }}">
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                        <select name="id_fakultas" id="id_fakultas" class="w-full px-3 py-2 border border-gray-300 rounded-lg {{ $disabledClass }}" {{ $disabledAttr }}>
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id_fakultas }}" {{ $peserta->id_fakultas == $f->id_fakultas ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                        @if($isLulus)
                            <input type="hidden" name="id_fakultas" value="{{ $peserta->id_fakultas }}">
                        @endif
                    </div>
                    <div class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                        <select name="id_prodi" id="id_prodi" class="w-full px-3 py-2 border border-gray-300 rounded-lg {{ $disabledClass }}" {{ $disabledAttr }}>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id_prodi }}" {{ $peserta->id_prodi == $p->id_prodi ? 'selected' : '' }}>
                                    {{ $p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        @if($isLulus)
                            <input type="hidden" name="id_prodi" value="{{ $peserta->id_prodi }}">
                        @endif
                    </div>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ url('PmbMstPendaftarans/list-user/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    @if(!$isLulus)
    $('#id_jalur').on('change', function() {
        let jalurId = $(this).val();
        $('#id_gelombang').empty().append('<option value="">-- Pilih Gelombang --</option>');
        $('#id_fakultas').empty().append('<option value="">-- Pilih Fakultas --</option>');
        $('#id_prodi').empty().append('<option value="">-- Pilih Program Studi --</option>');
        if (jalurId) {
            $.get("{{ url('get-gelombang-by-jalur') }}/" + jalurId, function(data) {
                $.each(data, function(index, item) {
                    $('#id_gelombang').append('<option value="'+ item.id_gelombang +'">'+ item.nama_gelombang +'</option>');
                });
            });
        }
    });

    $('#id_gelombang').on('change', function() {
        let jalurId = $('#id_jalur').val();
        let gelombangId = $(this).val();
        $('#id_fakultas').empty().append('<option value="">-- Pilih Fakultas --</option>');
        $('#id_prodi').empty().append('<option value="">-- Pilih Program Studi --</option>');
        if (jalurId && gelombangId) {
            $.get("{{ url('get-fakultas-by-jalur-gelombang') }}/" + jalurId + "/" + gelombangId, function(data) {
                $.each(data, function(index, item) {
                    $('#id_fakultas').append('<option value="'+ item.id_fakultas +'">'+ item.nama_fakultas +'</option>');
                });
            });
        }
    });

    $('#id_fakultas').on('change', function() {
        let jalurId = $('#id_jalur').val();
        let gelombangId = $('#id_gelombang').val();
        let fakultasId = $(this).val();
        $('#id_prodi').empty().append('<option value="">-- Pilih Program Studi --</option>');
        if (jalurId && gelombangId && fakultasId) {
            $.get("{{ url('get-prodi-by-jalur-gelombang-fakultas') }}/" + jalurId + "/" + gelombangId + "/" + fakultasId, function(data) {
                $.each(data, function(index, item) {
                    $('#id_prodi').append('<option value="'+ item.id_prodi +'">'+ item.nama_prodi +'</option>');
                });
            });
        }
    });
    @else
    $('#id_jalur, #id_gelombang, #id_fakultas, #id_prodi').prop('disabled', true).addClass('cursor-not-allowed');
    @endif
});
</script>
@endsection
