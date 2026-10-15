<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PSB {{ config('client.name') }} - Penerimaan Siswa Baru 2025/2026</title>
    <link rel="icon" type="image/png" href="{{ asset(config('client.logo')) }}">
    <script src="https://cdn.tailwindcss.com"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pmb-green': {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        ol {
    list-style: decimal !important;
    margin-left: 1.25rem;
}


    </style>
</head>
<body class="font-sans">
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ url('/') }}" class="no-underline flex items-center gap-3">
                    <img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}" class="h-12 w-12 object-contain">
                    <div class="leading-tight">
                        <div class="text-2xl md:text-3xl font-bold text-pmb-green-700">PSB {{ config('client.name') }}</div>
                        <div class="text-xs text-gray-500">{{ config('client.region') }}</div>
                    </div>
                </a>
                <div class="hidden md:flex space-x-8">
                   <a href="{{url('/')}}" 
   class="no-underline text-pmb-green-700 font-semibold border-b-2 border-pmb-green-700 pb-1">
   Home
</a>

<a href="{{url('/enroll')}}" 
   class="no-underline text-gray-600 hover:text-pmb-green-700 transition-colors">
   Pendaftaran
</a>

<div class="relative inline-block ml-6 group">
    <button class="no-underline text-gray-600 hover:text-pmb-green-700 transition-colors font-medium">
        Brosur
    </button>
    <div class="absolute hidden group-hover:block hover:block bg-white border border-gray-200 shadow-lg rounded mt-2 min-w-[180px] z-50">
        @forelse($brosurs as $b)
            <a href="{{ asset('storage/'.$b->brosur) }}" 
               download="{{ basename($b->brosur) }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 no-underline">
               {{ basename($b->brosur) }}
            </a>
        @empty
            <span class="block px-4 py-2 text-sm text-gray-400">Belum ada brosur</span>
        @endforelse
    </div>
</div>


                </div>
             <div class="flex items-center space-x-2 text-gray-600 hover:text-pmb-green-700 transition-colors cursor-pointer">
   @if(Auth::check())
    @if(Auth::user()->role === 'admin')
        <a href="{{ url('/pages/display/home') }}" class="no-underline flex items-center space-x-1">
            <span class="font-medium">Dashboard</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    @elseif(Auth::user()->role === 'peserta')
        <a href="{{ url('/pages/dashboard') }}" class="no-underline flex items-center space-x-1">
            <span class="font-medium">Dashboard</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    @endif
@else
    <a href="{{ url('/ServiceLogin') }}" class="no-underline flex items-center space-x-1">
        <span class="font-medium">Masuk</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </a>
@endif

</div>


            </div>
        </div>
    </nav>

    <main>

<section class="relative overflow-hidden w-full">
    <div class="relative w-full h-0" style="padding-bottom: 42%;">
        <div id="slider" class="absolute top-0 left-0 w-full h-full flex transition-transform duration-700 ease-in-out">
            @if(count($sliders) > 0)
                <div class="w-full h-full flex-shrink-0">
                    <img src="{{ asset('storage/'.$sliders->last()->image) }}" 
                         class="w-full h-full object-cover">
                </div>
                @foreach($sliders as $s)
                    <div class="w-full h-full flex-shrink-0">
                        <img src="{{ asset('storage/'.$s->image) }}" 
                             class="w-full h-full object-cover">
                    </div>
                @endforeach
                <div class="w-full h-full flex-shrink-0">
                    <img src="{{ asset('storage/'.$sliders->first()->image) }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif
        </div>

        <button onclick="prevSlide()" 
            class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-10 h-10 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-2xl sm:text-3xl z-10 transition-all">‹
        </button>

        <button onclick="nextSlide()" 
            class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-10 h-10 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-2xl sm:text-3xl z-10 transition-all">›
        </button>
    </div>
</section>

<script>
    const slider = document.getElementById('slider');
    const slides = slider.children;
    let currentSlide = 1;
    const totalSlides = slides.length;
    let isTransitioning = false;
    
    slider.style.transform = `translateX(-${currentSlide * 100}%)`;

    function updateSlide() {
        if(isTransitioning) return;
        isTransitioning = true;
        slider.style.transition = 'transform 0.7s ease-in-out';
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function nextSlide() {
        if(isTransitioning) return;
        currentSlide++;
        updateSlide();
        if(currentSlide === totalSlides - 1) {
            setTimeout(() => {
                slider.style.transition = 'none';
                currentSlide = 1;
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                setTimeout(() => {
                    isTransitioning = false;
                }, 50);
            }, 700);
        } else {
            setTimeout(() => {
                isTransitioning = false;
            }, 700);
        }
    }

    function prevSlide() {
        if(isTransitioning) return;
        currentSlide--;
        updateSlide();
        if(currentSlide === 0) {
            setTimeout(() => {
                slider.style.transition = 'none';
                currentSlide = totalSlides - 2;
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                setTimeout(() => {
                    isTransitioning = false;
                }, 50);
            }, 700);
        } else {
            setTimeout(() => {
                isTransitioning = false;
            }, 700);
        }
    }

    let autoSlide = setInterval(nextSlide, 5500);

    document.querySelector('button[onclick="prevSlide()"]').addEventListener('click', () => {
        clearInterval(autoSlide);
        autoSlide = setInterval(nextSlide, 5500);
    });

    document.querySelector('button[onclick="nextSlide()"]').addEventListener('click', () => {
        clearInterval(autoSlide);
        autoSlide = setInterval(nextSlide, 5500);
    });
</script>

        <section class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid lg:grid-cols-2 gap-16">
                    <div>
                        <div class="text-pmb-green-600 font-semibold mb-4 text-lg">Alur</div>
                        <h2 class="text-4xl font-bold text-pmb-green-800 mb-10">Pendaftaran</h2>
                        
                        <div class="space-y-8">
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 bg-pmb-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">1</div>
                                <div>
                                   <div class="text-gray-800 text-lg">
    Mendaftar melalui link: 
    <a href="{{ url('/') }}" class="no-underline text-pmb-green-600 hover:text-pmb-green-700" target="_blank">
        {{ parse_url(url('/'), PHP_URL_HOST) }}
    </a>
</div>
 </div>
                            </div>
                            
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 bg-pmb-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">2</div>
                                <div class="text-gray-800 text-lg">Melakukan pembayaran pendaftaran di virtual account</div>
                            </div>
                            
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 bg-pmb-green-600 text-white rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">3</div>
                                <div class="text-gray-800 text-lg">Melengkapi data diri</div>
                            </div>
                            
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">4</div>
                                <div class="text-gray-800 text-lg">Melakukan Daftar Ulang</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-pmb-green-600 font-semibold mb-4 text-lg text-right">Tanggal</div>
                        <h2 class="text-4xl font-bold text-pmb-green-800 mb-10 text-right leading-tight">
                            Penerimaan<br>Siswa<br>Baru
                        </h2>
                        
                     <div class="space-y-6">
    @forelse($gelombangTahunIni as $g)
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="font-bold text-gray-800 text-lg mb-2">Gelombang {{ $g->gelombang }}</div>
            <div class="text-gray-600">
                {{ \Carbon\Carbon::parse($g->start)->format('d F Y') }}
                -
                {{ \Carbon\Carbon::parse($g->end)->format('d F Y') }}
            </div>
        </div>
    @empty
        <div class="p-6 bg-yellow-100 rounded-xl text-yellow-800">
            Belum ada gelombang di tahun ini.
        </div>
    @endforelse
</div>

                    </div>
                </div>
            </div>
        </section>
<section class="max-w-7xl mx-auto mt-6 px-4 mb-5">
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg border border-green-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-5">
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi Terbaru
            </h2>
        </div>
        
        <div class="p-8">
            @forelse($informasi as $item)
                <div class="mb-5 last:mb-0 bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl hover:border-green-300 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-8">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="prose max-w-none">
                                    <p class="text-gray-700 text-base leading-relaxed whitespace-pre-line">{{ $item->informasi }}</p>
                                </div>
                                
                                @if($item->created_at)
                                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center gap-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-5">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg font-medium">Belum ada informasi tersedia</p>
                    <p class="text-gray-400 text-base mt-2">Informasi akan ditampilkan di sini</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

      <section class="bg-pmb-green-700 py-24">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <!-- Header -->
        <div class="text-yellow-400 font-semibold mb-8 text-lg">Kenapa harus PSB {{ config('client.name') }}?</div>
        
        <!-- Visi Section -->
        <h2 class="text-5xl font-bold text-white mb-8">Visi</h2>
        <div class="bg-white/10 rounded-2xl p-8 mb-20 max-w-4xl mx-auto border border-white/20">
            <div class="text-2xl text-white leading-relaxed font-medium">
                Mewujudkan generasi Qur'ani, cerdas, berakhlak mulia, dan berprestasi
            </div>
        </div>
        
        <!-- Misi Section -->
        <h2 class="text-5xl font-bold text-white mb-16">Misi</h2>
        
        <div class="grid md:grid-cols-2 gap-6 text-left max-w-5xl mx-auto">
            <div class="space-y-6">
                <div class="bg-white/5 rounded-xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-pmb-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-white text-lg leading-relaxed">Pembinaan karakter dan agama intensif</div>
                    </div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-pmb-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-white text-lg leading-relaxed">Pengintegrasian IMTAQ dan IPTEK</div>
                    </div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-pmb-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-white text-lg leading-relaxed">Peningkatan kompetensi akademik dan non-akademik</div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white/5 rounded-xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-pmb-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7-293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-white text-lg leading-relaxed">Pengembangan kemampuan berbahasa</div>
                    </div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-pmb-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-white text-lg leading-relaxed">Melaksanakan kajian dan riset</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <section id="registration" class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-pmb-green-600 font-semibold mb-4 text-lg">Syarat & ketentuan</div>
                <h2 class="text-4xl font-bold text-pmb-green-800 mb-12">Seputar Pendaftaran</h2>
                
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="flex flex-wrap border-b border-gray-200" id="tabNavigation">
                        <button onclick="showTab('syarat')" id="tab-syarat" class="px-8 py-6 bg-pmb-green-600 text-white font-semibold hover:bg-pmb-green-700 transition-colors flex-1 min-w-max">
                            Syarat Pendaftaran
                        </button>
                        <button onclick="showTab('pembayaran')" id="tab-pembayaran" class="px-8 py-6 text-gray-600 hover:bg-gray-50 transition-colors flex-1 min-w-max">
                            Petunjuk Pembayaran
                        </button>
                        <button onclick="showTab('daftar-ulang')" id="tab-daftar-ulang" class="px-8 py-6 text-gray-600 hover:bg-gray-50 transition-colors flex-1 min-w-max">
                            Persyaratan Daftar Ulang
                        </button>
                    </div>
                    
                    <div class="p-8" id="tabContent">
                        <!-- Syarat Pendaftaran -->
                        <div id="content-syarat" class="tab-content">
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <span class="text-pmb-green-600 font-bold text-lg flex-shrink-0">1.</span>
                                    <div class="text-gray-700 text-lg">Scan foto berseragam sekolah</div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <span class="text-pmb-green-600 font-bold text-lg flex-shrink-0">2.</span>
                                    <div class="text-gray-700 text-lg">Mengisi formulir pendaftaran online</div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <span class="text-pmb-green-600 font-bold text-lg flex-shrink-0">3.</span>
                                    <div class="text-gray-700 text-lg">Membayar biaya pendaftaran</div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <span class="text-pmb-green-600 font-bold text-lg flex-shrink-0">4.</span>
                                    <div class="text-gray-700 text-lg leading-relaxed">
    Transaksi ke bank Muamalat baik teller, ATM, e-banking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transaksi virtual akun. 
    <span class="font-bold text-red-600">Biaya Pendaftaran sebesar Rp. xxx dibayarkan non-tunai (langsung ke nomor virtual akun)</span>
</div>

                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <span class="text-pmb-green-600 font-bold text-lg flex-shrink-0">5.</span>
                                   <div class="text-gray-700 text-lg leading-relaxed">
    Transaksi melalui LINTAS BANK baik teller, ATM, e-banking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transfer antar bank. 
    <span class="font-bold text-red-600">Biaya Pendaftaran menjadi Rp. xxx + biaya transfer antar bank Rp. 6.500</span>
</div>

                                </div>
                            </div>
                        </div>

                        <!-- Petunjuk Pembayaran -->
                      <div id="content-pembayaran" class="tab-content hidden">
  <div class="card card-body p-4 bg-white shadow-sm rounded">

    <p class="pb-3 fw-bold fs-5">Petunjuk Pembayaran</p>

    <p class="pb-2 fw-bold fs-6 mt-3">Pembayaran melalui Payment Point / Teller Bank Muamalat:</p>
    <ol class="ps-3">
        <li class="mb-2">Tunjukkan Nomor Pendaftaran / Virtual Akun (VA) Anda ke teller</li>
        <li class="mb-2">Isi form pembayaran (jumlah nominal pembayaran sesuai dengan informasi dari sistem PSB Online)</li>
        <li class="mb-2">Simpan bukti struk sebagai bukti pembayaran yang sah</li>
    </ol>

    <p class="pb-2 fw-bold fs-6 mt-4">Pembayaran melalui ATM Muamalat:</p>
    <ol class="ps-3">
        <li class="mb-2">Masukkan PIN</li>
        <li class="mb-2">Pilih menu "PEMBAYARAN" lalu pilih menu "VIRTUAL ACCOUNT".</li>
        <li class="mb-2">Masukkan "Nomor Pendaftaran / Virtual Akun (VA)" contoh: 7977xx12345678901</li>
        <li class="mb-2">Periksa Informasi pembayaran pastikan VA sudah sesuai dengan informasi (cek info nama dan nama tagihan)</li>
        <li class="mb-2">Tekan "YA" jika setuju dengan informasi pembayaran.</li>
        <li class="mb-2">Masukkan nominal pembayaran sesuai dengan total bayar yang tertera.</li>
        <li class="mb-2">Kemudian tekan "Benar".</li>
        <li class="mb-2">Konfirmasi transaksi sukses.</li>
        <li class="mb-2">Simpan struk ATM sebagai bukti pembayaran yang sah.</li>
    </ol>

    <p class="pb-2 fw-bold fs-6 mt-4">Pembayaran melalui Jaringan ATM BERSAMA, PRIMA (BCA, Mandiri, BNI, BRI, dll):</p>
    <ol class="ps-3">
        <li class="mb-2">Masukkan PIN</li>
        <li class="mb-2">Pilih menu "Transaksi Lainnya " lalu pilih "TRANSFER"</li>
        <li class="mb-2">Pilih Menu "KE REK BANK LAIN / ANTAR BANK ONLINE"</li>
        <li class="mb-2">Masukkan Kode Bank Muamalat (147) dilanjut dengan 16 digit nomor Virtual Account (VA) contoh: 7977xx12345678901</li>
        <li class="mb-2">Masukkan nominal pembayaran sesuai dengan total bayar yang tertera.</li>
        <li class="mb-2">Periksa Informasi pembayaran pastikan VA sudah sesuai dengan informasi (cek info nama dan nama tagihan)</li>
        <li class="mb-2">Jika data sudah benar maka lakukan konfirmasi pada data transfer.</li>
        <li class="mb-2">Simpan struk ATM sebagai bukti pembayaran.</li>
    </ol>

    <p class="pb-2 fw-bold fs-6 mt-4">Pembayaran melalui M-Banking Muamalat:</p>
    <ol class="ps-3">
        <li class="mb-2">Login ke Mobile Banking Muamalat (Muamalat DIN).</li>
        <li class="mb-2">Pilih menu "BELI atau BAYAR".</li>
        <li class="mb-2">Pilih menu "VIRTUAL ACCOUNT".</li>
        <li class="mb-2">Masukkan nomor "Virtual Account" contoh: 7977xx12345678901.</li>
        <li class="mb-2">Periksa Informasi pembayaran pastikan VA sudah sesuai dengan informasi (cek info nama dan nama tagihan).</li>
        <li class="mb-2">Masukkan nominal pembayaran sesuai dengan total bayar yang tertera.</li>
        <li class="mb-2">Masukkan TIN mobile banking anda.</li>
        <li class="mb-2">Simpan bukti transaksi sebagai bukti pembayaran.</li>
    </ol>

    <p class="pb-2 fw-bold fs-6 mt-4">Pembayaran melalui Internet Banking/Mobile Banking (Realtime/Online Transfer):</p>
    <ol class="ps-3">
        <li class="mb-2">Login ke Internet Banking</li>
        <li class="mb-2">Pilih Menu "Transfer"</li>
        <li class="mb-2">Pilih Menu "Ke Rek Bank Lain / Realtime Transfer"</li>
        <li class="mb-2">Pilih “Seluruh Channel Bank” sebagai rekening tujuan</li>
        <li class="mb-2">Masukkan Nomor Pendaftaran / Virtual Account. contoh: 75100012345678901</li>
        <li class="mb-2">Masukkan jumlah sesuai tagihan.</li>
        <li class="mb-2">Simpan bukti struk sebagai bukti pembayaran yang sah.</li>
    </ol>
  </div>
</div>


                        <!-- Persyaratan Daftar Ulang -->
                        <div id="content-daftar-ulang" class="tab-content hidden">
                           <div class="card card-body p-4 bg-white shadow-sm rounded">
    <p class="pb-3 fw-bold fs-5">Syarat Daftar Ulang</p>
    <ol class="ps-3">
        <li class="mb-2">
            Lulus tes seleksi dibuktikan dengan Surat Keputusan Panitia PSB
        </li>
        <li class="mb-2">
            Membawa berkas sebagai berikut :
            <ol type="a" class="ps-3 mt-2">
                <li class="mb-1">Surat Keterangan Lulus dari Panitia PSB</li>
                <li class="mb-1">Scan KTP/KK</li>
                <li class="mb-1">Scan Ijazah SMA/SMK/MA beserta Transkrip Nilai</li>
            </ol>
        </li>
    </ol>
</div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

@include('footer')

    <!-- WhatsApp Floating Button -->
    <div class="fixed bottom-6 right-6 z-50">
    <a href="https://wa.me/6289513300501?text=Halo%20DEMO%20PMB,%20saya%20ingin%20bertanya%20tentang%20pendaftaran%20mahasiswa%20baru" 
       target="_blank" 
       class="bg-green-500 hover:bg-green-600 text-white w-16 h-16 flex items-center justify-center rounded-full shadow-2xl transform hover:scale-110 transition-all duration-300 animate-pulse hover:animate-none group relative">
        
        <!-- Ikon WhatsApp -->
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.106"/>
        </svg>

        <!-- Tooltip -->
        <div class="absolute -top-12 right-0 bg-gray-800 text-white text-sm px-3 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
            Chat WhatsApp
            <div class="absolute top-full right-3 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-800"></div>
        </div>
    </a>
</div>


    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active style from all tabs
            const tabs = document.querySelectorAll('#tabNavigation button');
            tabs.forEach(tab => {
                tab.classList.remove('bg-pmb-green-600', 'text-white');
                tab.classList.add('text-gray-600', 'hover:bg-gray-50');
            });
            
            // Show selected tab content
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            
            // Add active style to selected tab
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.remove('text-gray-600', 'hover:bg-gray-50');
            activeTab.classList.add('bg-pmb-green-600', 'text-white');
        }

        // Smooth scroll function
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Apply animation to elements
            const animatedElements = document.querySelectorAll('section > div > *');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // Add floating animation to WhatsApp button
            const waButton = document.querySelector('.fixed.bottom-6.right-6 a');
            if (waButton) {
                setInterval(() => {
                    waButton.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        waButton.style.transform = 'scale(1)';
                    }, 200);
                }, 3000);
            }

            // Add click tracking
            document.querySelectorAll('a[href^="tel:"], a[href^="https://wa.me/"]').forEach(link => {
                link.addEventListener('click', function() {
                    console.log('Contact clicked:', this.href);
                });
            });
        });

        // Add hover effects for cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover\\:shadow-xl');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Mobile menu functionality (if needed)
        function toggleMobileMenu() {
            // Add mobile menu toggle functionality here if needed
            console.log('Mobile menu toggled');
        }

        // Form validation (if forms are added later)
        function validateForm(formData) {
            // Add form validation logic here
            return true;
        }

        // Scroll to top functionality
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                // Show scroll to top button if needed
            }
        });
    </script>

    <style>
        /* Custom CSS for enhanced styling */
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Smooth transitions */
        * {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #15803d;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #166534;
        }

        /* Loading animation for images */
        img {
            transition: opacity 0.3s ease;
        }

        img[src=""] {
            opacity: 0;
        }

        /* Enhanced button hover effects */
        button:hover {
            transform: translateY(-1px);
        }

        /* Tab content animation */
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* WhatsApp button pulse animation */
        .animate-pulse-custom {
            animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-custom {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .8;
                transform: scale(1.05);
            }
        }
    </style>
    @include('partials.session-guard')
</body>
</html>