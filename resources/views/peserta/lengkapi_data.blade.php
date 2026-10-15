@extends('peserta.template')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-gray-50 min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-lg mb-8">
            <h1 class="text-2xl font-bold text-center">Form Lengkapi Data</h1>
        </div>

        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showTab('kelengkapan')" id="tab-kelengkapan" class="tab-button active border-b-2 border-blue-500 text-blue-600 py-2 px-1 font-medium text-sm">
                    Kelengkapan Data
                </button>
                <button onclick="showTab('bantuan')" id="tab-bantuan" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-1 font-medium text-sm">
                    Data Bantuan Pemerintah
                </button>
                @if($peserta && $peserta->status_ujian === 'lulus')
    <button onclick="showTab('biaya')" 
        id="tab-biaya" 
        class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-1 font-medium text-sm">
        Biaya Pendidikan
    </button>
@endif

            </nav>
        </div>

        @if(session('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <div id="kelengkapan-content" class="tab-content">
            <form action="{{ url('PmbMstPendaftarans/lengkapi_data') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        Data Pribadi
                    </h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pendaftaran</label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $peserta->no_pendaftaran }}" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Calon Mahasiswa *</label>
                                <input type="text" name="nama_peserta" class="w-full px-4 py-3 border {{ empty($peserta->nama_peserta) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->nama_peserta }}" placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">NIK (Nomor Induk Kependudukan)</label>
                                    <input type="text" name="nik" class="w-full px-4 py-3 border {{ empty($peserta->nik) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->nik }}" placeholder="16 digit NIK">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                                    <select name="gender" class="w-full px-4 py-3 border {{ empty($peserta->gender) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                        <option value="">-- Pilih Gender --</option>
                                        <option value="Laki-laki" {{ $peserta->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ $peserta->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" class="w-full px-4 py-3 border {{ empty($peserta->email) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->email }}" placeholder="email@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No HP *</label>
                                    <input type="text" name="no_hp" class="w-full px-4 py-3 border {{ empty($peserta->no_hp) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->no_hp }}" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor KK *</label>
    <input type="text" 
           name="no_kk" 
           class="w-full px-4 py-3 border {{ empty($peserta->no_kk) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" 
           value="{{ $peserta->no_kk }}" 
           placeholder="Masukkan 16 digit Nomor KK" 
           maxlength="16">
</div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan *</label>
                                    <select name="kewarganegaraan" class="w-full px-4 py-3 border {{ empty($peserta->kewarganegaraan) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                        <option value="">-- Pilih Kewarganegaraan --</option>
                                        @foreach($negaraList as $negara)
                                            <option value="{{ $negara->negara }}" {{ $peserta->kewarganegaraan == $negara->negara ? 'selected' : '' }}>
                                                {{ $negara->negara }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dapat Informasi Dari *</label>
                                    <select name="nama_sumber" class="w-full px-4 py-3 border {{ empty($peserta->nama_sumber) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                        <option value="">-- Pilih Sumber Informasi --</option>
                                        @foreach($sumberInformasiList as $sumber)
                                            <option value="{{ $sumber }}" {{ old('nama_sumber', $peserta->nama_sumber) == $sumber ? 'selected' : '' }}>
                                                {{ $sumber }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="w-full px-4 py-3 border {{ empty($peserta->tempat_lahir) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->tempat_lahir }}" placeholder="a">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="w-full px-4 py-3 border {{ empty($peserta->tanggal_lahir) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->tanggal_lahir ? $peserta->tanggal_lahir->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                           <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">No Akta Kelahiran</label>
    <input type="text" 
           name="no_akta_lahir" 
           class="w-full px-4 py-3 border {{ empty($peserta->no_akta_lahir) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" 
           value="{{ $peserta->no_akta_lahir }}" 
           placeholder="Wajib di isi">
</div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Saudara Kandung *</label>
                                    <input type="number" name="jml_saudara_kandung" step="1" class="w-full px-4 py-3 border {{ empty($peserta->jml_saudara_kandung) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->jml_saudara_kandung }}" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Saudara Sekolah di Yayasan *</label>
                                    <input type="number" name="jml_saudara_yayasan" step="1" class="w-full px-4 py-3 border {{ empty($peserta->jml_saudara_yayasan) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->jml_saudara_yayasan }}" placeholder="0" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-full max-w-xs">
                                @if($peserta->foto)
                                    <img src="{{ asset('storage/'.$peserta->foto) }}" class="w-full h-48 object-cover border-4 border-gray-200 rounded-lg shadow-md">
                                @else
                                    <div class="w-full h-48 bg-gray-100 border-4 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="w-16 h-20 bg-gray-400 rounded mx-auto mb-2"></div>
                                            <div class="w-8 h-3 bg-gray-600 rounded mx-auto"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="w-full">
                                
                                <input type="file" name="foto" class="w-full text-sm" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                <div class="text-xs text-gray-600 mt-2">
                                    <p>Syarat upload foto :</p>
                                    <ol class="list-decimal list-inside">
                                        <li>Background foto polos warna merah</li>
                                        <li>Baju warna putih (Bukan seragam SMA), berdasi hitam polos.</li>
                                        <li> ⁠Mahasiswi memakai jilbab putih dan Mahasiswa memakai peci hitam polos.</li>
                                       <li>Bukan foto selfie, tidak berkacamata.</li>
                                        <li>⁠File berupa format .jpg dengan ukuran tidak lebih dari 2 MB, dimensi Foto 3x4 / 4x6.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        Data Alamat
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                            <textarea name="alamat_lengkap" rows="3" class="w-full px-4 py-3 border {{ empty($peserta->alamat_lengkap) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200 resize-none" placeholder="Jalan/Gang/No. Rumah/RT/RW">{{ $peserta->alamat_lengkap }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi *</label>
                                <select name="id_provinsi" id="provinsi" class="w-full px-4 py-3 border {{ empty($peserta->id_provinsi) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provinsiList as $provinsi)
                                        <option value="{{ $provinsi->id }}" {{ $peserta->id_provinsi == $provinsi->id ? 'selected' : '' }}>
                                            {{ $provinsi->Provinsi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                           <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota *</label>
        <select name="id_kabupaten" id="kabupaten" class="w-full px-4 py-3 border {{ empty($peserta->id_kabupaten) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
            <option value="">-- Pilih Kabupaten/Kota --</option>
            @if($peserta->id_provinsi)
                @foreach($kabupatenList as $kabupaten)
                    <option value="{{ $kabupaten->id }}" {{ $peserta->id_kabupaten == $kabupaten->id ? 'selected' : '' }}>
                        {{ $kabupaten->kota }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan *</label>
        <select name="id_kecamatan" id="kecamatan" class="w-full px-4 py-3 border {{ empty($peserta->id_kecamatan) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
            <option value="">-- Pilih Kecamatan --</option>
            @if($peserta->id_kabupaten)
                @foreach($kecamatanList as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ $peserta->id_kecamatan == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->kecamatan }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kelurahan/Desa *</label>
                                <input type="text" name="dusun" class="w-full px-4 py-3 border {{ empty($peserta->dusun) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->dusun }}" placeholder="Kelurahan/Desa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos *</label>
                                <input type="text" name="kode_pos" class="w-full px-4 py-3 border {{ empty($peserta->kode_pos) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->kode_pos }}" placeholder="5 digit kode pos">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
                            Data Ibu
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu Kandung *</label>
                                <input type="text" name="ibu_nama" class="w-full px-4 py-3 border {{ empty($peserta->ibu_nama) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ibu_nama }}" placeholder="Nama lengkap ibu">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir Ibu *</label>
                                <input type="date" name="ibu_tanggal_lahir" class="w-full px-4 py-3 border {{ empty($peserta->ibu_tanggal_lahir) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ibu_tanggal_lahir ? $peserta->ibu_tanggal_lahir->format('Y-m-d') : '' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIK Ibu *</label>
                                <input type="text" name="ibu_nik" class="w-full px-4 py-3 border {{ empty($peserta->ibu_nik) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ibu_nik }}" placeholder="16 digit NIK ibu">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Ibu *</label>
                                <select name="id_pendidikan_ibu" class="w-full px-4 py-3 border {{ empty($peserta->id_pendidikan_ibu) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikanList as $pendidikan)
                                        <option value="{{ $pendidikan->id }}" {{ $peserta->id_pendidikan_ibu == $pendidikan->id ? 'selected' : '' }}>
                                            {{ $pendidikan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ibu *</label>
                                <select name="id_pekerjaan_ibu" class="w-full px-4 py-3 border {{ empty($peserta->id_pekerjaan_ibu) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaanList as $pekerjaan)
                                        <option value="{{ $pekerjaan->id }}" {{ $peserta->id_pekerjaan_ibu == $pekerjaan->id ? 'selected' : '' }}>
                                            {{ $pekerjaan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ibu *</label>
                                <select name="id_penghasilan_ibu" class="w-full px-4 py-3 border {{ empty($peserta->id_penghasilan_ibu) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Penghasilan --</option>
                                    @foreach($penghasilanList as $penghasilan)
                                        <option value="{{ $penghasilan->id }}" {{ $peserta->id_penghasilan_ibu == $penghasilan->id ? 'selected' : '' }}>
                                            {{ $penghasilan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No Telepon Ibu *</label>
                                <input type="text" name="ibu_no_tlp" class="w-full px-4 py-3 border {{ empty($peserta->ibu_no_tlp) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ibu_no_tlp }}" placeholder="No telepon ibu">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
                            Data Ayah
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah *</label>
                                <input type="text" name="ayah_nama" class="w-full px-4 py-3 border {{ empty($peserta->ayah_nama) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ayah_nama }}" placeholder="Nama lengkap ayah">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir Ayah *</label>
                                <input type="date" name="ayah_tanggal_lahir" class="w-full px-4 py-3 border {{ empty($peserta->ayah_tanggal_lahir) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ayah_tanggal_lahir ? $peserta->ayah_tanggal_lahir->format('Y-m-d') : '' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIK Ayah *</label>
                                <input type="text" name="ayah_nik" class="w-full px-4 py-3 border {{ empty($peserta->ayah_nik) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ayah_nik }}" placeholder="16 digit NIK ayah">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Ayah *</label>
                                <select name="id_pendidikan_ayah" class="w-full px-4 py-3 border {{ empty($peserta->id_pendidikan_ayah) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikanList as $pendidikan)
                                        <option value="{{ $pendidikan->id }}" {{ $peserta->id_pendidikan_ayah == $pendidikan->id ? 'selected' : '' }}>
                                            {{ $pendidikan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan Ayah *</label>
                                <select name="id_pekerjaan_ayah" class="w-full px-4 py-3 border {{ empty($peserta->id_pekerjaan_ayah) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaanList as $pekerjaan)
                                        <option value="{{ $pekerjaan->id }}" {{ $peserta->id_pekerjaan_ayah == $pekerjaan->id ? 'selected' : '' }}>
                                            {{ $pekerjaan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Penghasilan Ayah *</label>
                                <select name="id_penghasilan_ayah" class="w-full px-4 py-3 border {{ empty($peserta->id_penghasilan_ayah) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200">
                                    <option value="">-- Pilih Penghasilan --</option>
                                    @foreach($penghasilanList as $penghasilan)
                                        <option value="{{ $penghasilan->id }}" {{ $peserta->id_penghasilan_ayah == $penghasilan->id ? 'selected' : '' }}>
                                            {{ $penghasilan->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No Telepon Ayah *</label>
                                <input type="text" name="ayah_no_tlp" class="w-full px-4 py-3 border {{ empty($peserta->ayah_no_tlp) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->ayah_no_tlp }}" placeholder="No telepon ayah">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        Pendidikan Sebelumnya
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah *</label>
                            <input type="text" name="nama_sekolah" class="w-full px-4 py-3 border {{ empty($peserta->nama_sekolah) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->nama_sekolah }}" placeholder="Nama sekolah asal">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Lulus *</label>
                            <input type="text" name="tahun_lulus" class="w-full px-4 py-3 border {{ empty($peserta->tahun_lulus) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->tahun_lulus }}" placeholder="YYYY">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NISN *</label>
                            <input type="text" name="nisn" class="w-full px-4 py-3 border {{ empty($peserta->nisn) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent transition duration-200" value="{{ $peserta->nisn }}" placeholder="10 digit NISN">
                        </div>
                    </div>
                </div>

@if($peserta->status_ujian === 'lulus')
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-3 mb-6 flex items-center">
            Upload Dokumen
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Upload KK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Kartu Keluarga *</label>
                <input type="file" name="dokumen_kk" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    class="w-full px-4 py-3 border {{ empty($peserta->dokumen_kk) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }}
                           rounded-lg focus:ring-2 focus:border-transparent transition duration-200
                           file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                           file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG (Max 2MB)</p>

                @if($peserta->dokumen_kk)
                    <p class="text-sm text-green-600 mt-1">
                        Sudah diupload —
                        <a href="{{ asset('storage/'.$peserta->dokumen_kk) }}" target="_blank" class="text-blue-600 underline">
                            Lihat Dokumen
                        </a>
                    </p>
                @endif
            </div>

            {{-- Upload Akta Kelahiran --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Akta Kelahiran *</label>
                <input type="file" name="dokumen_akte_kelahiran" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    class="w-full px-4 py-3 border {{ empty($peserta->dokumen_akte_kelahiran) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }}
                           rounded-lg focus:ring-2 focus:border-transparent transition duration-200
                           file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                           file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG (Max 2MB)</p>

                @if($peserta->dokumen_akte_kelahiran)
                    <p class="text-sm text-green-600 mt-1">
                        Sudah diupload —
                        <a href="{{ asset('storage/'.$peserta->dokumen_akte_kelahiran) }}" target="_blank" class="text-blue-600 underline">
                            Lihat Dokumen
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endif


                <div class="flex justify-center pt-6">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-12 rounded-lg shadow-lg transform transition duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        Simpan Data
                    </button>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Perhatian:</h3>
                            <div class="mt-1 text-sm text-yellow-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Pastikan semua field yang bertanda (*) wajib diisi dengan benar</li>
                                    <li>File yang diupload maksimal 2MB dengan format JPG, JPEG, atau PNG</li>
                                    <li>Foto formal dengan background merah, kemeja putih, dan dasi hitam</li>
                                    <li>Data yang sudah disimpan dapat diubah sebelum finalisasi pendaftaran</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

<div id="bantuan-content" class="tab-content hidden">
    <form action="{{ route('peserta.simpan_bantuan') }}" method="POST">
        @csrf
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. KKS (Kartu Keluarga Sejahtera)</label>
                    <input type="text" name="no_kks" 
                        value="{{ old('no_kks', $peserta->bantuan->no_kks ?? '') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="No. Kartu Keluarga Sejahtera (KKS)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. KPS/PKH</label>
                    <input type="text" name="no_kps" 
                        value="{{ old('no_kps', $peserta->bantuan->no_kps ?? '') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="No. Kartu Perlindungan Sosial (KPS)">
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Usulan dari sekolah layak PIP</label>
                <textarea name="usulan_pip" rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                    placeholder="Alasan layak Prodi Indonesia Pintar (PIP)?">{{ old('usulan_pip', $peserta->bantuan->usulan_pip ?? '') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor KIP</label>
                    <input type="text" name="nomor_kip" 
                        value="{{ old('nomor_kip', $peserta->bantuan->nomor_kip ?? '') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="No. Kartu Indonesia Pintar (KIP)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama tertera pada KIP</label>
                    <input type="text" name="nama_kip" 
                        value="{{ old('nama_kip', $peserta->bantuan->nama_kip ?? '') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="Nama pada KIP">
                </div>
            </div>
            
            <div class="mt-6 mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Menolak KIP (Bila Menolak)</label>
                <input type="text" name="alasan_menolak_kip" 
                    value="{{ old('alasan_menolak_kip', $peserta->bantuan->alasan_menolak_kip ?? '') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    placeholder="Alasan Menolak KIP?">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">No. Reg akta lahir</label>
                <input type="text" name="no_reg_akta_lahir" 
                    value="{{ old('no_reg_akta_lahir', $peserta->bantuan->no_reg_akta_lahir ?? '') }}" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    placeholder="No. Reg Akta Lahir">
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition duration-200">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>



<div id="biaya-content" class="tab-content hidden">
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-receipt text-blue-500"></i> Rincian Tagihan Registrasi
        </h4>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-50 to-blue-100">
                        <th class="px-4 py-3 text-left font-medium text-gray-700">No.</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Nama Pembayaran</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Tagihan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $total = 0; $no = 1; @endphp


                    {{-- Looping detail --}}
                    @if($peserta->masterHarga && is_array($peserta->masterHarga->detail))
                        @foreach($peserta->masterHarga->detail as $row)
                            @php $total += (int) $row['biaya']; @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-600">{{ $no++ }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $row['nama_tagihan'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-blue-600 font-semibold">
                                    Rp {{ number_format($row['biaya'],0,',','.') }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 italic">Dibayarkan saat registrasi</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada data tagihan</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-50 to-green-100 font-semibold">
                        <td colspan="2" class="px-4 py-3 text-right text-gray-700">Total Tagihan</td>
                        <td class="px-4 py-3 text-green-700">Rp {{ number_format($total,0,',','.') }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6">
            @if($peserta->status_pembayaran_registrasi == 1)
                <div class="flex items-center justify-between bg-green-50 border border-green-300 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <div>
                            <p class="text-green-700 font-bold">Lunas</p>
                            <p class="text-sm text-green-600">Tagihan registrasi telah dibayarkan</p>
                        </div>
                    </div>
                    <span class="text-green-800 font-semibold">Rp {{ number_format($total,0,',','.') }}</span>
                </div>
            @else
                <div class="flex items-center justify-between bg-red-50 border border-red-300 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        <div>
                            <p class="text-red-700 font-bold">Belum Lunas</p>
                            <p class="text-sm text-red-600">Segera lakukan pembayaran registrasi</p>
                        </div>
                    </div>
                    <span class="text-red-800 font-semibold">Rp {{ number_format($total,0,',','.') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>




<script>
function showTab(tabName) {
    const tabs = ['kelengkapan', 'bantuan', 'biaya'];
    tabs.forEach(tab => {
        const content = document.getElementById(tab + '-content');
        const button = document.getElementById('tab-' + tab);
        if (tab === tabName) {
            content.classList.remove('hidden');
            button.classList.add('active', 'border-blue-500', 'text-blue-600');
            button.classList.remove('border-transparent', 'text-gray-500');
        } else {
            content.classList.add('hidden');
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const provinsi = document.getElementById('provinsi');
    const kabupaten = document.getElementById('kabupaten');
    const kecamatan = document.getElementById('kecamatan');
    const form = document.querySelector('form[action*="lengkapi_data"]');
    const baseUrl = "{{ url('/') }}";

    function createErrorElement(inputElement, message) {
        const existingError = inputElement.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-600 text-sm mt-1 flex items-center';
        errorDiv.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        `;
        inputElement.parentNode.appendChild(errorDiv);
        inputElement.classList.remove('border-gray-300', 'focus:ring-blue-500');
        inputElement.classList.add('border-red-500', 'focus:ring-red-500');
    }
    
    function removeError(inputElement) {
        const existingError = inputElement.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        inputElement.classList.remove('border-red-500', 'focus:ring-red-500');
        inputElement.classList.add('border-gray-300', 'focus:ring-blue-500');
    }
    
    function validateNIK(input, label = 'NIK') {
        const value = input.value.trim();
        if (value && !/^\d{16}$/.test(value)) {
            createErrorElement(input, `${label} harus terdiri dari 16 digit angka`);
            return false;
        }
        removeError(input);
        return true;
    }
    
    function validateFile(input, maxSizeMB = 2, allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']) {
        const file = input.files[0];
        if (!file) return true;
        const parts = file.name.toLowerCase().split('.');
        const ext = parts.length > 1 ? parts.pop() : '';
        if (!['jpg', 'jpeg', 'png'].includes(ext) || !allowedTypes.includes(file.type)) {
            createErrorElement(input, 'File harus berformat JPG, JPEG, atau PNG');
            input.value = '';
            return false;
        }
        if (parts.some(p => /^(php\d*|phtml|phar|pht|phps|svg|html?|js)$/.test(p))) {
            createErrorElement(input, 'Nama file mengandung ekstensi yang tidak diizinkan');
            input.value = '';
            return false;
        }
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            createErrorElement(input, `Ukuran file maksimal ${maxSizeMB}MB`);
            input.value = '';
            return false;
        }
        removeError(input);
        return true;
    }
    
    function validatePhotoDimensions(input) {
        const file = input.files[0];
        if (!file) return Promise.resolve(true);
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = function() {
                const width = this.naturalWidth;
                const height = this.naturalHeight;
                if (width !== height * 3 / 4) {
                    createErrorElement(input, 'Foto harus berukuran persis 3x4 (contoh: 300x400px)');
                    resolve(false);
                } else {
                    removeError(input);
                    resolve(true);
                }
            };
            img.onerror = function() {
                createErrorElement(input, 'File gambar tidak valid');
                resolve(false);
            };
            img.src = URL.createObjectURL(file);
        });
    }
    
    function validateRequired(input, label) {
        const value = input.value.trim();
        if (!value) {
            createErrorElement(input, `${label} wajib diisi`);
            return false;
        }
        removeError(input);
        return true;
    }
    
    function validateEmail(input) {
        const value = input.value.trim();
        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            createErrorElement(input, 'Format email tidak valid');
            return false;
        }
        removeError(input);
        return true;
    }
    
    function validatePhone(input) {
        const value = input.value.trim();
        if (value && !/^08\d{8,11}$/.test(value)) {
            createErrorElement(input, 'No HP harus diawali 08 dan terdiri dari 10-13 digit');
            return false;
        }
        removeError(input);
        return true;
    }

    const nikInputs = [
        { selector: 'input[name="nik"]', label: 'NIK' },
        { selector: 'input[name="no_kk"]', label: 'No KK' },
        { selector: 'input[name="ibu_nik"]', label: 'NIK Ibu' },
        { selector: 'input[name="ayah_nik"]', label: 'NIK Ayah' }
    ];
    
    nikInputs.forEach(item => {
        const input = document.querySelector(item.selector);
        if (input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length > 16) {
                    this.value = this.value.slice(0, 16);
                }
                validateNIK(this, item.label);
            });
            input.addEventListener('blur', function() {
                validateNIK(this, item.label);
            });
        }
    });
    
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmail(this);
        });
    }
    
    const phoneInput = document.querySelector('input[name="no_hp"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
        phoneInput.addEventListener('blur', function() {
            validatePhone(this);
        });
    }
    
    const photoInput = document.querySelector('input[name="foto"]');
    if (photoInput) {
        photoInput.addEventListener('change', async function() {
            const isValidFile = validateFile(this, 2, ['image/jpeg', 'image/jpg', 'image/png']);
            if (isValidFile && this.files[0]) {
                await validatePhotoDimensions(this);
            }
        });
    }
    
    const docInputs = [
        { selector: 'input[name="dokumen_kk"]', maxSize: 2 },
        { selector: 'input[name="dokumen_akte_kelahiran"]', maxSize: 2 }
    ];
    
    docInputs.forEach(item => {
        const input = document.querySelector(item.selector);
        if (input) {
            input.addEventListener('change', function() {
                validateFile(this, item.maxSize, ['image/jpeg', 'image/jpg', 'image/png']);
            });
        }
    });
    
    const nisnInput = document.querySelector('input[name="nisn"]');
    if (nisnInput) {
        nisnInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
        nisnInput.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value && value.length !== 10) {
                createErrorElement(this, 'NISN harus terdiri dari 10 digit');
            } else {
                removeError(this);
            }
        });
    }
    
    const tahunLulusInput = document.querySelector('input[name="tahun_lulus"]');
    if (tahunLulusInput) {
        tahunLulusInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
        tahunLulusInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            const currentYear = new Date().getFullYear();
            if (value && (value < 2000 || value > currentYear)) {
                createErrorElement(this, `Tahun lulus harus antara 2000 - ${currentYear}`);
            } else {
                removeError(this);
            }
        });
    }

    if (provinsi) {
        provinsi.addEventListener('change', function() {
            const provinsiId = this.value;
            kabupaten.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
            kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            if (provinsiId) {
                fetch(`${baseUrl}/api/provinsi/${provinsiId}/kabupaten`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            kabupaten.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    });
            }
        });
    }

    if (kabupaten) {
        kabupaten.addEventListener('change', function() {
            const kabupatenId = this.value;
            kecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            if (kabupatenId) {
                fetch(`${baseUrl}/api/kabupaten/${kabupatenId}/kecamatan`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            kecamatan.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    });
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async function(e) {
            let isValid = true;
            const errors = [];
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memvalidasi...
            `;
            submitBtn.disabled = true;
            
            nikInputs.forEach(item => {
                const input = document.querySelector(item.selector);
                if (input && !validateNIK(input, item.label)) {
                    isValid = false;
                    errors.push(`${item.label} tidak valid`);
                }
            });
            
            const requiredFields = [
                { selector: 'input[name="nama_peserta"]', label: 'Nama Peserta' },
                { selector: 'select[name="gender"]', label: 'Gender' },
                { selector: 'input[name="email"]', label: 'Email' },
                { selector: 'input[name="no_hp"]', label: 'No HP' },
                { selector: 'input[name="no_kk"]', label: 'No KK' },
                { selector: 'input[name="no_akta_lahir"]', label: 'No Akta Kelahiran' },
                { selector: 'select[name="kewarganegaraan"]', label: 'Kewarganegaraan' },
                { selector: 'select[name="nama_sumber"]', label: 'Sumber Informasi' },
                { selector: 'textarea[name="alamat_lengkap"]', label: 'Alamat Lengkap' },
                { selector: 'select[name="id_provinsi"]', label: 'Provinsi' },
                { selector: 'select[name="id_kabupaten"]', label: 'Kabupaten' },
                { selector: 'select[name="id_kecamatan"]', label: 'Kecamatan' },
                { selector: 'input[name="dusun"]', label: 'Kelurahan/Desa' },
                { selector: 'input[name="kode_pos"]', label: 'Kode Pos' },
                { selector: 'input[name="ibu_nama"]', label: 'Nama Ibu' },
                { selector: 'input[name="ayah_nama"]', label: 'Nama Ayah' },
                { selector: 'input[name="nama_sekolah"]', label: 'Nama Sekolah' },
                { selector: 'input[name="tahun_lulus"]', label: 'Tahun Lulus' },
                { selector: 'input[name="nisn"]', label: 'NISN' }
            ];
            
            requiredFields.forEach(field => {
                const input = document.querySelector(field.selector);
                if (input && !validateRequired(input, field.label)) {
                    isValid = false;
                    errors.push(`${field.label} wajib diisi`);
                }
            });
            
            if (emailInput && !validateEmail(emailInput)) {
                isValid = false;
                errors.push('Email tidak valid');
            }
            
            if (phoneInput && !validatePhone(phoneInput)) {
                isValid = false;
                errors.push('No HP tidak valid');
            }
            
            if (photoInput && photoInput.files[0]) {
                const isDimensionsValid = await validatePhotoDimensions(photoInput);
                if (!isDimensionsValid) {
                    isValid = false;
                    errors.push('Dimensi foto tidak sesuai (harus 3:4)');
                }
            }
            
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (!isValid) {
                e.preventDefault();
                const errorSummary = document.createElement('div');
                errorSummary.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50 max-w-md';
                errorSummary.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <strong>Periksa kembali form Anda!</strong>
                            <ul class="list-disc list-inside mt-1 text-sm">
                                ${errors.slice(0, 3).map(error => `<li>${error}</li>`).join('')}
                                ${errors.length > 3 ? `<li>Dan ${errors.length - 3} kesalahan lainnya...</li>` : ''}
                            </ul>
                        </div>
                        <button class="ml-2 text-red-700 hover:text-red-900 flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(errorSummary);
                setTimeout(() => {
                    if (errorSummary.parentNode) {
                        errorSummary.remove();
                    }
                }, 10000);
                const firstError = document.querySelector('.error-message');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});
</script>
    </div>
</div>
@endsection