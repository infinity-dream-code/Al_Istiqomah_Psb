@extends('peserta.template')

@section('content')
@php
    $step1Done = $step1Done ?? false;
    $step2Done = $step2Done ?? false;
    $step3Done = $step3Done ?? false;
    $step4Done = $step4Done ?? false;
    $saldoAmount = isset($saldoVa) && $saldoVa ? $saldoVa->amount() : null;
@endphp
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white px-6 py-6 border-b">
        <h1 class="text-3xl text-gray-400 font-light">Timeline <span class="text-lg text-gray-400 ml-2">View your Actions</span></h1>
    </div>

    <div class="px-6 py-8">
        <div class="max-w-5xl mx-auto">
            {{-- Kartu Saldo VA --}}
            <div class="mb-8 bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Saldo Virtual Akun</h2>
                <p class="text-sm text-gray-500 mb-3">Monitor pembayaran cicilan melalui VA sebelum memakai web tagihan.</p>
                <div class="flex flex-wrap gap-6">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-400">Nomor VA</div>
                        <div class="text-base font-semibold text-gray-800">{{ $peserta->va_number ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-400">Saldo</div>
                        @if($saldoAmount !== null)
                            <div class="text-base font-semibold text-green-700">Rp {{ number_format($saldoAmount, 0, ',', '.') }}</div>
                        @else
                            <div class="text-base font-semibold text-gray-500">Saldo belum tersedia</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute left-32 top-0 bottom-0 w-0.5 bg-blue-300 hidden md:block"></div>
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-blue-300 md:hidden"></div>

                {{-- INFO --}}
                <div class="relative flex items-start mb-16">
                    <div class="hidden md:block w-32 text-right pr-6 flex-shrink-0">
                        <div class="text-blue-400 font-medium text-lg mb-1">INFO</div>
                        <div class="text-gray-500 text-sm">
                            {{ optional(Auth::user() instanceof \App\Models\DataPeserta ? Auth::user() : Auth::user()->peserta)->created_at?->translatedFormat('j F Y') ?? '-' }}
                        </div>
                    </div>
                    <div class="absolute left-6 md:relative md:left-0 z-10 w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg border-4 border-white flex-shrink-0">
                        i
                    </div>
                    <div class="ml-16 md:ml-8 flex-1">
                        <div class="block md:hidden mb-4">
                            <div class="text-blue-400 font-medium text-lg mb-1 ml-4">INFO</div>
                            <div class="text-gray-500 text-sm ml-4">
                                {{ optional(Auth::user() instanceof \App\Models\DataPeserta ? Auth::user() : Auth::user()->peserta)->created_at?->translatedFormat('j F Y') ?? '-' }}
                            </div>
                        </div>
                        <div class="bg-blue-500 text-white px-5 py-3 rounded-t-lg">
                            <h3 class="font-semibold text-lg">Wajib Gabung Group</h3>
                        </div>
                        <div class="bg-white p-5 border border-gray-200 rounded-b-lg shadow-sm">
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                Silahkan bagi seluruh calon siswa yang telah mendaftar dapat bertanya pada link dibawah ini :
                            </p>
                            <p class="text-blue-600 font-semibold mb-4">Gabung Group disini</p>
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                untuk bergabung dengan group harap install aplikasi WhatsApp terlebih dahulu.
                            </p>
                            <p class="text-red-600 font-semibold mb-4 leading-relaxed">
                                Waspada terhadap segala jenis penipuan!. Pembayaran hanya dilakukan melalui VIRTUAL AKUN yang didapat setelah melakukan pendaftaran.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                Penipuan sering terjadi lewat chating dengan modus membayarkan biaya registrasi dan ancaman terhadap kelulusan, pembayaran registrasi hanya di trasfer pada <strong>VIRTUAL AKUN ( {{ $peserta->va_number ?? '-' }} )</strong> bukan rekening yang lain.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- STEP 1 --}}
                <div class="relative flex items-start mb-16">
                    <div class="hidden md:block w-32 text-right pr-6 flex-shrink-0">
                        <div class="{{ $step1Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1">STEP 1</div>
                        <div class="text-gray-500 text-sm ml-4">
                            {{ $peserta && $peserta->tgl_bayar_daftar ? \Carbon\Carbon::parse($peserta->tgl_bayar_daftar)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    <div class="absolute left-6 md:relative md:left-0 z-10 w-12 h-12 {{ $step1Done ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white flex-shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-16 md:ml-8 flex-1">
                        <div class="block md:hidden mb-4">
                            <div class="{{ $step1Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1 ml-4">STEP 1</div>
                            <div class="text-gray-500 text-sm ml-4">
                                {{ $peserta && $peserta->tgl_bayar_daftar ? \Carbon\Carbon::parse($peserta->tgl_bayar_daftar)->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>
                        <div class="{{ $step1Done ? 'bg-green-500' : 'bg-red-500' }} text-white px-5 py-3 rounded-t-lg">
                            <h3 class="font-semibold text-lg">Notifikasi Pembayaran</h3>
                        </div>
                        <div class="bg-white p-5 border border-gray-200 rounded-b-lg shadow-sm">
                            <p class="text-gray-700 mb-2">Yth. <span class="text-blue-600 font-medium">{{ $peserta->nama_peserta ?? '-' }}</span></p>
                            @if($step1Done)
                                <p class="text-gray-700">Status Pembayaran Pendaftaranmu Berhasil.</p>
                            @else
                                <p class="text-gray-700">Menunggu pembayaran biaya pendaftaran melalui Virtual Akun.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- STEP 2 --}}
                <div class="relative flex items-start mb-16">
                    <div class="hidden md:block w-32 text-right pr-6 flex-shrink-0">
                        <div class="{{ $step2Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1">STEP 2</div>
                        <div class="text-gray-500 text-sm">{{ optional($peserta)->created_at?->translatedFormat('j F Y') ?? '-' }}</div>
                    </div>
                    <div class="absolute left-6 md:relative md:left-0 z-10 w-12 h-12 {{ $step2Done ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white flex-shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-16 md:ml-8 flex-1">
                        <div class="block md:hidden mb-4">
                            <div class="{{ $step2Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1 ml-4">STEP 2</div>
                            <div class="text-gray-500 text-sm ml-4">{{ optional($peserta)->created_at?->translatedFormat('j F Y') ?? '-' }}</div>
                        </div>
                        <div class="{{ $step2Done ? 'bg-green-500' : 'bg-red-500' }} text-white px-5 py-3 rounded-t-lg">
                            <h3 class="font-semibold text-lg">Kelengkapan Info Pendaftaran</h3>
                        </div>
                        <div class="bg-white p-5 border border-gray-200 rounded-b-lg shadow-sm">
                            <p class="text-gray-700 mb-4">Lengkapilah isian berikut :</p>
                            <div class="text-gray-700 mb-4 space-y-1">
                                <div>1. Data Pribadi</div>
                                <div>2. Data Tempat Tinggal dan Orangtua</div>
                                <div>3. Data Sekolah</div>
                            </div>
                            <p class="text-gray-600 text-sm mb-1">* Lengkapilah Isian tersebut termasuk Upload File FOTO</p>
                            <p class="text-gray-600 text-sm mb-4">** Foto wajib di upload.</p>
                            @if($step2Done)
                                <p class="text-green-600 text-sm font-semibold mb-2">Data kamu sudah lengkap.</p>
                                <a href="{{ url('/PmbMstPendaftarans/lengkapi_data') }}"
                                   class="inline-block bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded text-sm font-medium transition-colors duration-200">
                                    Lihat / Perbarui Data
                                </a>
                            @else
                                <p class="text-red-600 text-sm font-semibold mb-4">Data kamu belum lengkap. Silahkan cek dan lengkapi.</p>
                                <a href="{{ url('/PmbMstPendaftarans/lengkapi_data') }}"
                                   class="inline-block bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded text-sm font-medium transition-colors duration-200">
                                    Yuk Lengkapi
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div class="relative flex items-start mb-16">
                    <div class="hidden md:block w-32 text-right pr-6 flex-shrink-0">
                        <div class="{{ $step3Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1">STEP 3</div>
                        <div class="text-gray-500 text-sm">
                            @if($peserta?->ujian?->isNotEmpty() && $peserta->ujian->first()->tanggal)
                                {{ $peserta->ujian->first()->tanggal->translatedFormat('j F Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="absolute left-6 md:relative md:left-0 z-10 w-12 h-12 {{ $step3Done ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white flex-shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-16 md:ml-8 flex-1">
                        <div class="block md:hidden mb-4">
                            <div class="{{ $step3Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1 ml-4">STEP 3</div>
                            <div class="text-gray-500 text-sm ml-4">
                                @if($peserta?->ujian?->isNotEmpty() && $peserta->ujian->first()?->tanggal)
                                    {{ $peserta->ujian->first()->tanggal->translatedFormat('j F Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="{{ $step3Done ? 'bg-green-500' : 'bg-red-500' }} text-white px-5 py-3 rounded-lg">
                            <h3 class="font-semibold text-lg">TES SELEKSI</h3>
                        </div>
                        @php $ujianTerbaru = $peserta?->ujian?->sortByDesc('tanggal')->first(); @endphp
                        @if($ujianTerbaru)
                            <div class="mb-3 mt-4">
                                <p class="text-gray-700 text-sm">Nomor Pendaftaran:
                                    <span class="font-semibold">{{ $peserta->no_pendaftaran }}</span>
                                </p>
                            </div>
                            <a href="{{ route('ujian.showKartu', $peserta->no_pendaftaran) }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                                <i class="fas fa-id-card mr-2"></i> Cetak Kartu Ujian
                            </a>
                        @else
                            <p class="text-gray-700 italic mt-3">Menunggu jadwal ujian.</p>
                        @endif
                    </div>
                </div>

                {{-- STEP 4 --}}
                <div class="relative flex items-start">
                    <div class="hidden md:block w-32 text-right pr-6 flex-shrink-0">
                        <div class="{{ $step4Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1">STEP 4</div>
                        <div class="text-gray-500 text-sm">
                            {{ $peserta?->relasiGelombang?->pengumuman
                                ? $peserta->relasiGelombang->pengumuman->translatedFormat('j F Y')
                                : '-' }}
                        </div>
                    </div>
                    <div class="absolute left-6 md:relative md:left-0 z-10 w-12 h-12 {{ $step4Done ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white flex-shrink-0">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.75 2.524z"></path>
                        </svg>
                    </div>
                    <div class="ml-16 md:ml-8 flex-1">
                        <div class="block md:hidden mb-4">
                            <div class="{{ $step4Done ? 'text-green-500' : 'text-red-500' }} font-medium text-lg mb-1 ml-4">STEP 4</div>
                            <div class="text-gray-500 text-sm ml-4">
                                {{ optional($peserta?->relasiGelombang?->pengumuman)->translatedFormat('j F Y') ?? '-' }}
                            </div>
                        </div>
                        <div class="{{ $step4Done ? 'bg-green-500' : 'bg-red-500' }} text-white px-5 py-3 rounded-t-lg">
                            <h3 class="font-semibold text-lg">Pengumuman Kelulusan</h3>
                        </div>
                        <div class="bg-white p-5 border border-gray-200 rounded-b-lg shadow-sm">
                            @if($peserta && in_array(strtolower((string) $peserta->status_ujian), ['lulus', 'gagal'], true))
                                @if(strtolower($peserta->status_ujian) === 'lulus')
                                    <p class="text-green-600 font-semibold mb-4">Selamat, Anda dinyatakan LULUS.</p>
                                @else
                                    <p class="text-red-600 font-semibold mb-4">Maaf, Anda dinyatakan TIDAK LULUS.</p>
                                @endif
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('peserta.surat.kelulusan') }}" target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                                        Cetak Surat Keterangan
                                    </a>
                                    @if(strtolower($peserta->status_ujian) === 'lulus')
                                        <a href="{{ route('peserta.surat.registrasi') }}" target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">
                                            Cetak Surat Registrasi
                                        </a>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-700">Menunggu Pengumuman.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
