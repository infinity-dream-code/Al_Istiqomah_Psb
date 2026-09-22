<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Pendaftaran Siswa Baru - DEMO PSB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .section-header {
            background: linear-gradient(135deg, #2d5a3d 0%, #1f3d2a 100%);
        }
        
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        
        .form-section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transition: box-shadow 0.3s ease;
        }
        
        .form-input {
            border: 1.5px solid #d1d5db;
            padding: 12px 16px;
            border-radius: 8px;
            width: 100%;
            background-color: #fafbfc;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input:hover:not(:focus) {
            border-color: #9ca3af;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
            transform: translateY(-1px);
        }
        
        .required { color: #dc2626; }
        
        .subsection {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
        }
        
        .label-text {
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }
        
        .section-title {
            color: #1f2937;
            font-weight: 600;
            font-size: 16px;
        }
        
        .blue-title {
            color: #3b82f6;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="form-section mb-8 overflow-hidden">
                <div class="section-header text-white px-8 py-8 text-center">
                    <h1 class="text-3xl font-bold mb-2">DEMO PSB</h1>
                    <p class="text-green-100 text-lg">Form Pendaftaran Siswa Baru</p>
                    <div class="mt-4 w-20 h-1 bg-white/30 mx-auto rounded-full"></div>
                </div>
            </div>

            <form id="enrollmentForm" method="post" action="{{ route('enroll') }}" class="space-y-8">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-6 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <strong class="font-semibold">Terjadi kesalahan!</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-section">
                    <div class="section-header text-white px-6 py-4">
                        <h3 class="text-lg font-semibold">Pilihan</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="blue-title mb-6">Jalur & Sekolah & Jurusan</h4>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                             <div>
            <label class="block label-text mb-2">Jalur Pendaftaran <span class="required">*</span></label>
            <select name="id_jalur" id="jalur" required class="form-input">
                <option value="">PILIH JALUR PENDAFTARAN</option>
                @foreach($jalurs as $j)
                    <option value="{{ $j->id_jalur }}">{{ $j->nama_jalur }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block label-text mb-2">Gelombang <span class="required">*</span></label>
            <select name="id_gelombang" id="gelombang" required class="form-input" disabled>
                <option value="">PILIH GELOMBANG</option>
            </select>
        </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                 <div>
            <label class="block label-text mb-2">Sekolah <span class="required">*</span></label>
            <select name="id_fakultas" id="fakultas" required class="form-input" disabled>
                <option value="">PILIH SEKOLAH</option>
            </select>
        </div>
                               <div>
            <label class="block label-text mb-2">Jurusan <span class="required">*</span></label>
            <select name="id_prodi" id="prodi" required class="form-input" disabled>
                <option value="">PILIH JURUSAN</option>
            </select>
        </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header text-white px-6 py-4">
                        <h3 class="text-lg font-semibold">Biodata & Informasi Pendidikan Sebelumnya</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="blue-title mb-6">Biodata</h4>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block label-text mb-2">Nama Calon Siswa <span class="required">*</span></label>
                                <input type="text" name="nama" required placeholder="NAMA SESUAI DENGAN AKTA LAHIR" class="form-input">
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block label-text mb-2">Jenis Kelamin <span class="required">*</span></label>
                                    <select name="gender" required class="form-input">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Tempat Lahir <span class="required">*</span></label>
                                    <input type="text" name="tempat_lahir" required placeholder="Contoh: Jakarta" class="form-input">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block label-text mb-2">Tanggal Lahir <span class="required">*</span></label>
                                    <input type="date" name="tanggal_lahir" required class="form-input">
                                </div>
                                <div>
                                    <label class="block label-text mb-2">NISN <span class="required">*</span></label>
                                    <div class="flex gap-3">
                                        <input type="text" name="nisn" required placeholder="Nomor Induk Siswa Nasional" class="form-input">
                                        <button type="button" onclick="window.open('https://nisn.data.kemdikbud.go.id/index.php/Cindex/formcaribynama', '_blank')" class="btn-secondary whitespace-nowrap">Cek NISN</button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block label-text mb-2">Alamat Lengkap <span class="required">*</span></label>
                                <textarea name="alamat" required rows="3" placeholder="Alamat lengkap tempat tinggal" class="form-input resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block label-text mb-2">Provinsi <span class="required">*</span></label>
                                    <select id="provinsi" name="provinsi_id" required class="form-input">
                                        <option value="">Pilih provinsi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Kota/Kabupaten <span class="required">*</span></label>
                                    <select id="kabupaten" name="kabupaten_id" required disabled class="form-input">
                                        <option value="">Pilih kota/kabupaten</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Kecamatan <span class="required">*</span></label>
                                    <select id="kecamatan" name="kecamatan_id" required disabled class="form-input">
                                        <option value="">Pilih kecamatan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Rt/RW Dusun <span class="required">*</span></label>
                                    <input type="text" name="dusun" required placeholder="Contoh: RT 01/RW 02" class="form-input">
                                </div>
                            </div>

                            <h4 class="blue-title mb-6 mt-8">Pendidikan Sebelumnya</h4>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block label-text mb-2">Provinsi Sekolah <span class="required">*</span></label>
                                    <select id="provinsiSekolah" name="provinsi_sekolah_id" required class="form-input">
                                        <option value="">Pilih provinsi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Kota Sekolah <span class="required">*</span></label>
                                    <select id="kotaSekolah" name="kota_sekolah_id" required disabled class="form-input">
                                        <option value="">Pilih kota/kabupaten</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block label-text mb-2">Nama Sekolah Asal<span class="required">*</span></label>
                                    <input type="text" name="asal_sekolah" required placeholder="Contoh: SMA Negeri 1 Jakarta" class="form-input">
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Jurusan Sekolah Asal<span class=""></span></label>
                                    <select name="jurusan_sekolah" class="form-input">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusans as $j)
                                            <option value="{{ $j->id }}">{{ strtoupper($j->nama) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block label-text mb-2">Tahun Lulus <span class="required">*</span></label>
                                    <input type="text" name="tahun_lulus" required placeholder="2024" class="form-input">
                                </div>
                            </div>

                            <h4 class="blue-title mb-6 mt-8">Data Orangtua</h4>

                            <div class="subsection mb-6">
                                <h5 class="section-title mb-4">Data Ibu</h5>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block label-text mb-2">Nama Ibu <span class="required">*</span></label>
                                        <input type="text" name="nama_ibu" required placeholder="Nama lengkap ibu kandung" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">Pekerjaan Ibu <span class="required">*</span></label>
                                        <select name="pekerjaan_ibu" required class="form-input">
                                            <option value="">Pilih Pekerjaan</option>
                                            @foreach($pekerjaans as $p)
                                                <option value="{{ $p->id }}">{{ strtoupper($p->nama) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">Penghasilan Ibu <span class="required">*</span></label>
                                        <select name="penghasilan_ibu" required class="form-input">
                                            <option value="">Pilih Penghasilan</option>
                                            @foreach($penghasilans as $g)
                                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">No. Telp/WA Ibu <span class="required">*</span></label>
                                        <input type="text" name="tlp_ibu" required placeholder="08xxxxxxxxxx" class="form-input">
                                    </div>
                                </div>
                            </div>

                            <div class="subsection mb-6">
                                <h5 class="section-title mb-4">Data Ayah</h5>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block label-text mb-2">Nama Ayah <span class="required">*</span></label>
                                        <input type="text" name="nama_ayah" required placeholder="Nama lengkap ayah" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">Pekerjaan Ayah <span class="required">*</span></label>
                                        <select name="pekerjaan_ayah" required class="form-input">
                                            <option value="">Pilih Pekerjaan</option>
                                            @foreach($pekerjaans as $p)
                                                <option value="{{ $p->id }}">{{ strtoupper($p->nama) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">Penghasilan Ayah <span class="required">*</span></label>
                                        <select name="penghasilan_ayah" required class="form-input">
                                            <option value="">Pilih Penghasilan</option>
                                            @foreach($penghasilans as $g)
                                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block label-text mb-2">No. Telp/WA Ayah <span class="required">*</span></label>
                                        <input type="text" name="tlp_ayah" required placeholder="08xxxxxxxxxx" class="form-input">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <label class="block label-text mb-3">Verifikasi Keamanan <span class="required">*</span></label>
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                                    <span class="text-xl font-bold text-blue-600" id="captchaQuestion">2 + 1 =</span>
                                    <input type="text" name="captcha_answer" id="captchaInput" required placeholder="Jawaban" class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-center font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="generateCaptcha()" class="btn-secondary">🔄 Refresh</button>
                                </div>
                                <div id="captchaError" class="text-red-500 text-sm mt-2 hidden flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Jawaban salah, silakan coba lagi
                                </div>
                            </div>

                            <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-6 mt-8">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h5 class="font-semibold text-red-800 mb-2">Pernyataan Kesungguhan</h5>
                                        <p class="text-red-700 text-sm leading-relaxed">
                                            Dengan ini saya menyatakan dengan sesungguhnya bahwa semua informasi yang disampaikan dalam formulir ini adalah <strong>benar dan lengkap</strong>. Apabila ditemukan atau dibuktikan adanya penipuan atau pemalsuan atas informasi yang saya sampaikan, maka saya bersedia dikenakan sanksi sesuai ketentuan yang berlaku.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-8">
                                <button type="submit" class="btn-primary">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        DAFTAR SEKARANG
                                    </span>
                                </button>
                                <p class="text-gray-500 text-sm mt-3">Pastikan semua data telah diisi dengan benar sebelum mendaftar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('footer')

   <script>
let correctAnswer = 0;

function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10) + 1;
    const num2 = Math.floor(Math.random() * 10) + 1;
    const operation = Math.random() > 0.5 ? '+' : '-';
    if (operation === '+') {
        correctAnswer = num1 + num2;
        document.getElementById('captchaQuestion').textContent = `${num1} + ${num2} =`;
    } else {
        if (num1 >= num2) {
            correctAnswer = num1 - num2;
            document.getElementById('captchaQuestion').textContent = `${num1} - ${num2} =`;
        } else {
            correctAnswer = num2 - num1;
            document.getElementById('captchaQuestion').textContent = `${num2} - ${num1} =`;
        }
    }
    const captchaInput = document.getElementById('captchaInput');
    const captchaError = document.getElementById('captchaError');
    if (captchaInput) captchaInput.value = '';
    if (captchaError) captchaError.classList.add('hidden');
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-0 transition-transform duration-300`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 4000);
}

function resetSelect(element, placeholder) {
    if (!element) return;
    element.innerHTML = `<option value="">${placeholder}</option>`;
    element.disabled = true;
}

function fillSelect(element, items, placeholder, keyId = 'id', keyName = 'name') {
    resetSelect(element, placeholder);
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item[keyId];
        option.textContent = item[keyName].toUpperCase();
        element.appendChild(option);
    });
    element.disabled = false;
}

function fetchJson(url) {
    return fetch(url, { headers: { 'Accept': 'application/json' } }).then(response => response.json());
}

document.addEventListener('DOMContentLoaded', async function () {
    generateCaptcha();

    const form = document.getElementById('enrollmentForm');
    const captchaInput = document.getElementById('captchaInput');
    const captchaError = document.getElementById('captchaError');

    const provinsi = document.getElementById('provinsi');
    const kabupaten = document.getElementById('kabupaten');
    const kecamatan = document.getElementById('kecamatan');

    const provinsiSekolah = document.getElementById('provinsiSekolah');
    const kotaSekolah = document.getElementById('kotaSekolah');

    const jalurSelect = document.getElementById('jalur');
    const gelombangSelect = document.getElementById('gelombang');
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');

    if (jalurSelect && gelombangSelect) {
        jalurSelect.addEventListener('change', function () {
            const jalurId = this.value;
            resetSelect(gelombangSelect, 'PILIH GELOMBANG');
            resetSelect(fakultasSelect, 'PILIH SEKOLAH');
            resetSelect(prodiSelect, 'PILIH JURUSAN');
            if (jalurId) {
                fetch("{{ url('get-gelombang-by-jalur') }}/" + jalurId)
                .then(res => res.json())
                .then(data => {
                    fillSelect(gelombangSelect, data, 'PILIH GELOMBANG', 'id_gelombang', 'nama_gelombang');
                });
            }
        });
    }

    if (gelombangSelect && fakultasSelect) {
        gelombangSelect.addEventListener('change', function () {
            const jalurId = jalurSelect.value;
            const gelombangId = this.value;
            resetSelect(fakultasSelect, 'PILIH SEKOLAH');
            resetSelect(prodiSelect, 'PILIH JURUSAN');
            if (jalurId && gelombangId) {
                fetch("{{ url('get-fakultas-by-jalur-gelombang') }}/" + jalurId + "/" + gelombangId)
                .then(res => res.json())
                .then(data => {
                    fillSelect(fakultasSelect, data, 'PILIH SEKOLAH', 'id_fakultas', 'nama_fakultas');
                });
            }
        });
    }

    if (fakultasSelect && prodiSelect) {
        fakultasSelect.addEventListener('change', function () {
            const jalurId = jalurSelect.value;
            const gelombangId = gelombangSelect.value;
            const fakultasId = this.value;
            resetSelect(prodiSelect, 'PILIH JURUSAN');
            if (jalurId && gelombangId && fakultasId) {
                fetch("{{ url('get-prodi-by-jalur-gelombang-fakultas') }}/" + jalurId + "/" + gelombangId + "/" + fakultasId)
                .then(res => res.json())
                .then(data => {
                    fillSelect(prodiSelect, data, 'PILIH JURUSAN', 'id_prodi', 'nama_prodi');
                });
            }
        });
    }

    if (provinsi) {
        try {
            const provinces = await fetchJson(`{{ route('api.provinsi') }}`);
            fillSelect(provinsi, provinces, 'Pilih provinsi');
            resetSelect(kabupaten, 'Pilih kota/kabupaten');
            resetSelect(kecamatan, 'Pilih kecamatan');
        } catch (error) {
            resetSelect(provinsi, 'Gagal memuat provinsi');
            resetSelect(kabupaten, 'Pilih kota/kabupaten');
            resetSelect(kecamatan, 'Pilih kecamatan');
        }
        provinsi.addEventListener('change', async function(event) {
            const provinsiId = event.target.value;
            resetSelect(kabupaten, 'Pilih kota/kabupaten');
            resetSelect(kecamatan, 'Pilih kecamatan');
            if (!provinsiId) return;
            const url = `{{ route('api.kabupaten', ['provinsi' => '__ID__']) }}`.replace('__ID__', provinsiId);
            try {
                const kabupatens = await fetchJson(url);
                fillSelect(kabupaten, kabupatens, 'Pilih kota/kabupaten');
            } catch (error) {
                resetSelect(kabupaten, 'Gagal memuat kabupaten');
            }
        });
        if (kabupaten) {
            kabupaten.addEventListener('change', async function(event) {
                const kabupatenId = event.target.value;
                resetSelect(kecamatan, 'Pilih kecamatan');
                if (!kabupatenId) return;
                const url = `{{ route('api.kecamatan', ['kota' => '__ID__']) }}`.replace('__ID__', kabupatenId);
                try {
                    const kecamatans = await fetchJson(url);
                    fillSelect(kecamatan, kecamatans, 'Pilih kecamatan');
                } catch (error) {
                    resetSelect(kecamatan, 'Gagal memuat kecamatan');
                }
            });
        }
    }

    if (provinsiSekolah) {
        try {
            const provinces = await fetchJson(`{{ route('api.provinsi') }}`);
            fillSelect(provinsiSekolah, provinces, 'Pilih provinsi');
            resetSelect(kotaSekolah, 'Pilih kota/kabupaten');
        } catch (error) {
            resetSelect(provinsiSekolah, 'Gagal memuat provinsi');
            resetSelect(kotaSekolah, 'Pilih kota/kabupaten');
        }
        provinsiSekolah.addEventListener('change', async function(event) {
            const provinsiId = event.target.value;
            resetSelect(kotaSekolah, 'Pilih kota/kabupaten');
            if (!provinsiId) return;
            const url = `{{ route('api.kabupaten', ['provinsi' => '__ID__']) }}`.replace('__ID__', provinsiId);
            try {
                const kabupatens = await fetchJson(url);
                fillSelect(kotaSekolah, kabupatens, 'Pilih kota/kabupaten');
            } catch (error) {
                resetSelect(kotaSekolah, 'Gagal memuat kabupaten');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(event) {
            const userAnswer = parseInt(captchaInput ? captchaInput.value : '');
            if (!captchaInput || userAnswer !== correctAnswer || isNaN(userAnswer)) {
                event.preventDefault();
                if (captchaError) captchaError.classList.remove('hidden');
                generateCaptcha();
                showNotification('Captcha tidak valid, silakan coba lagi', 'error');
                return false;
            }
            if (captchaError) captchaError.classList.add('hidden');
            const requiredFields = form.querySelectorAll('[required]');
            let allValid = true;
            let firstInvalidField = null;
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500', 'bg-red-50');
                    field.classList.remove('border-gray-300');
                    allValid = false;
                    if (!firstInvalidField) firstInvalidField = field;
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    field.classList.add('border-gray-300');
                }
            });
            if (!allValid) {
                event.preventDefault();
                showNotification('Mohon lengkapi semua field yang wajib diisi', 'error');
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalidField.focus();
                }
                return false;
            }
            const emailField = document.querySelector('[name="email"]');
            const phoneFields = [
                document.querySelector('[name="tlp_ibu"]'),
                document.querySelector('[name="tlp_ayah"]')
            ];
            const nisnField = document.querySelector('[name="nisn"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    event.preventDefault();
                    showNotification('Format email tidak valid!', 'error');
                    emailField.focus();
                    return false;
                }
            }
            phoneFields.forEach((field, index) => {
                if (field && field.value) {
                    const phoneRegex = /^[0-9]{10,15}$/;
                    if (!phoneRegex.test(field.value.replace(/\s+/g, ''))) {
                        event.preventDefault();
                        const fieldName = index === 0 ? 'Nomor telepon ibu' : 'Nomor telepon ayah';
                        showNotification(`${fieldName} harus berisi 10-15 digit angka!`, 'error');
                        field.focus();
                        return false;
                    }
                }
            });
            if (nisnField && nisnField.value) {
                const nisnRegex = /^[0-9]{10}$/;
                if (!nisnRegex.test(nisnField.value)) {
                    event.preventDefault();
                    showNotification('NISN harus berisi 10 digit angka!', 'error');
                    nisnField.focus();
                    return false;
                }
            }
            showNotification('Form sedang diproses...', 'success');
        });
    }

    if (captchaInput) {
        captchaInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (captchaError) {
                if (this.value.trim() === '') {
                    captchaError.classList.add('hidden');
                    return;
                }
                if (value !== correctAnswer || isNaN(value)) {
                    captchaError.classList.remove('hidden');
                } else {
                    captchaError.classList.add('hidden');
                }
            }
        });
    }

    document.addEventListener('input', function(event) {
        if (event.target.hasAttribute('required')) {
            event.target.classList.remove('border-red-500', 'bg-red-50');
            event.target.classList.add('border-gray-300');
        }
    });

    const phoneInputs = document.querySelectorAll('input[name="tlp_ibu"], input[name="tlp_ayah"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    const nisnInput = document.querySelector('input[name="nisn"]');
    if (nisnInput) {
        nisnInput.addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
        });
    }

    const tahunLulusInput = document.querySelector('input[name="tahun_lulus"]');
    if (tahunLulusInput) {
        tahunLulusInput.addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 4);
        });
    }
});
</script>

@include('partials.session-guard')
</body>
</html>