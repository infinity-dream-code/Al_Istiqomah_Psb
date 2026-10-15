<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
</head>
<body>
     <footer class="bg-pmb-green-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-12">
               <div class=" text-white rounded-2xl p-8 shadow-lg">
    <h3 class="text-4xl font-extrabold text-yellow-400 mb-1 tracking-wide">PSB {{ config('client.name') }}</h3>
    <p class="text-gray-300 text-base mb-5">{{ config('client.region') }}</p>
    <p class="text-gray-300 text-lg leading-relaxed mb-6">
        Sistem PSB (Penerimaan Siswa Baru) adalah platform digital yang memudahkan calon siswa untuk melakukan pendaftaran dan memantau status pendaftaran hingga kelulusan secara online.
    </p>
    <ul class="space-y-4 text-gray-200 text-base">
        <li class="flex items-start">
            <span class="text-yellow-400 font-semibold mr-2">1.</span>
            <div>
                <span class="font-semibold text-white">Terintegrasi dengan Payment Gateway Bank</span><br>
                Siswa yang mendaftar langsung mendapatkan nominal pembayaran secara online dan dapat melakukan transaksi melalui nomor VA di berbagai channel bank.
            </div>
        </li>
        <li class="flex items-start">
            <span class="text-yellow-400 font-semibold mr-2">2.</span>
            <div>
                <span class="font-semibold text-white">Kemudahan Rekonsiliasi dan Identifikasi Pembayaran</span><br>
                Sekolah dapat dengan mudah memantau seluruh proses pendaftaran, termasuk jumlah registrasi, pembayaran, seleksi, dan kelulusan ujian masuk.
            </div>
        </li>
    </ul>
</div>

                <div>
                    <h4 class="text-2xl font-bold text-yellow-400 mb-6">SOCIAL MEDIA :</h4>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 hover:text-yellow-400 transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <span class="text-lg">@ict.smartpayment</span>
                        </div>
                        
                        <div class="flex items-center space-x-4 hover:text-yellow-400 transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <span class="text-lg">pt.inovasi cipta teknologi</span>
                        </div>
                        
                        <div class="flex items-center space-x-4 hover:text-yellow-400 transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-pmb-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <a href="{{ url('/') }}" target="_blank" class="no-underline text-lg hover:underline text-white">
    {{ parse_url(url('/'), PHP_URL_HOST) }}
</a>
</div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-2xl font-bold text-yellow-400 mb-6">KONTAK KAMI :</h4>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 hover:text-yellow-400 transition-colors">
                            <div class="w-12 h-12 bg-pmb-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <a href="#" class="no-underline text-lg hover:underline text-white">0895 1330 2299</a>
                        </div>
                        
                        <div class="flex items-center space-x-4 hover:text-yellow-400 transition-colors">
                            <div class="w-12 h-12 bg-pmb-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <a href="#" class="no-underline text-lg hover:underline text-white">0896 4404 8420</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-t border-pmb-green-700 mt-12 pt-8">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-400">
                <p class="text-lg">© 2025 Copyright: <a href="{{url('/')}}" class="no-underline text-yellow-400 hover:underline font-semibold">PSB {{ config('client.name') }}</a></p>
            </div>
        </div>
    </footer>

</body>
</html>