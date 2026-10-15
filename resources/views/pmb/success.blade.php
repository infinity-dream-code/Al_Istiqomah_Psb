<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Pendaftaran</title>
    <link rel="icon" type="image/png" href="{{ asset(config('client.logo')) }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto my-10">
    <!-- Informasi Pendaftaran -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-10">
        <div class="bg-green-900 px-6 py-4">
            <h2 class="text-white text-xl font-bold">INFORMASI PENDAFTARAN</h2>
        </div>
        <div class="px-8 py-6 space-y-6">
            <div>
                <p class="text-gray-600 font-semibold">Nama</p>
                <p class="text-lg font-bold">{{ $peserta->nama_peserta }}</p>
            </div>

            <div>
                <p class="text-gray-600 font-semibold">No. Pendaftaran</p>
                <p class="text-lg font-bold">{{ $peserta->no_pendaftaran }}</p>
            </div>

            <div>
                <p class="text-gray-600 font-semibold">No. Virtual Akun</p>
                <p class="text-lg font-bold">{{ $peserta->va_number ?: config('client.va_prefix') . $peserta->no_pendaftaran }}</p>
            </div>

            <div>
                <p class="text-gray-600 font-semibold">Akun Login Anda</p>
                <p class="text-lg font-bold">Username : {{ $peserta->no_pendaftaran }}</p>
                <p class="text-lg font-bold">Password : {{ $peserta->user->plain_password }}</p>
            </div>

            <div class="border-t pt-4">
                <p class="text-sm text-red-600 font-medium">
    NB: Simpanlah informasi penting ini. Gunakan Username dan Password untuk login pada alamat
    <a href="{{ url('ServiceLogin') }}" class="text-blue-600 underline" target="_blank">
        {{ parse_url(url('/'), PHP_URL_HOST) }}/ServiceLogin
    </a>
</p>
            </div>

            <div class="flex gap-3">
                <button onclick="window.print()" class="px-6 py-2 rounded-lg bg-green-700 text-white font-semibold hover:bg-green-800">
                    Cetak Info ini
                </button>
                <a href="{{url('/ServiceLogin')}}" class="px-6 py-2 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600">
                    Login
                </a>
                <a href="{{url('/')}}" class="px-6 py-2 rounded-lg bg-yellow-400 text-white font-semibold hover:bg-yellow-500">
                    Home
                </a>
            </div>
        </div>
    </div>

    <!-- Penting -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-cyan-500 px-6 py-4">
            <h2 class="text-white text-xl font-bold">PENTING!</h2>
        </div>
        <div class="px-8 py-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">Ketentuan</h3>
                <ul class="list-disc ml-6 text-gray-700 space-y-2">
                    <li>Nomor virtual akun di atas adalah nomor rekening virtual di bank Muamalat Indonesia bagi pendaftar yang digunakan untuk melakukan pembayaran biaya Pendaftaran PSB {{ config('client.name') }}.</li>
                    <li>Nomor virtual account juga digunakan untuk pembayaran sekolah bagi calon mahasiswa yang dinyatakan DITERIMA seleksi masuk di PSB {{ config('client.name') }}, maka mohon dicatat dan diingat-ingat nomor tersebut.</li>
                   <li>
    Petunjuk pembayaran dapat anda lihat di HOME PAGE 
    <a href="{{ url('/') }}" class="text-blue-600 underline" target="_blank">
        {{ parse_url(url('/'), PHP_URL_HOST) }}/guide
    </a>
</li>
<li>
    Bila anda sudah berhasil membayar, maka anda dapat login pada alamat 
    <a href="{{ url('ServiceLogin') }}" class="text-blue-600 underline" target="_blank">
        {{ parse_url(url('/'), PHP_URL_HOST) }}/ServiceLogin
    </a>
    untuk melihat status pembayaran.
</li>

                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Petunjuk Pembayaran</h3>
                <p class="text-gray-700 mb-2">Pembayaran dilakukan dengan 2 cara :</p>
                <ol class="list-decimal ml-6 text-gray-700 space-y-2">
    <li>
        Transaksi ke bank Muamalat baik teller, ATM, ibanking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transaksi virtual akun.
        <span class="font-bold">
            Biaya Pendaftaran sebesar Rp. {{ number_format($peserta->masterHarga->harga_final, 0, ',', '.') }}
            dibayarkan non-tunai (langsung ke nomor virtual akun di atas)
        </span>.
    </li>
    <li>
        Transaksi melalui LINTAS BANK baik teller, ATM, ibanking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transfer antar bank.
        <span class="font-bold">
            Biaya Pendaftaran menjadi Rp. {{ number_format($peserta->masterHarga->harga_final, 0, ',', '.') }} + biaya transfer lintas bank Rp. 6.500
        </span>.
    </li>
</ol>


                <p class="text-gray-700 mt-4">
                    Untuk transaksi antar bank (teller, ATM, ibanking, dll) bank biasanya membutuhkan kode bank tujuan (bank Muamalat), sehingga cara penulisan nomor rekening adalah kode bank diikuti nomor virtual akun.<br>
                    <span class="font-semibold">Sebagai berikut : {{ $peserta->va_number ?: config('client.va_prefix') . $peserta->no_pendaftaran }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
