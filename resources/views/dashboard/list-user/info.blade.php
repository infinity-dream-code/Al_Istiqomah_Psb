<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Akun Anda - PSB {{ $peserta->fakultas }}</title>
    <style>
        body {font-family: Arial, sans-serif;margin: 0;padding: 20px;background: #f0f0f0;line-height: 1.4;}
        .container {background: white;max-width: 650px;margin: 0 auto;padding: 0;box-shadow: 0 0 5px rgba(0,0,0,0.2);border: 1px solid #000;}
        .header {text-align: center;padding: 20px 20px 15px 20px;position: relative;}
        .date-info {position: absolute;top: 10px;right: 15px;font-size: 10px;text-align: right;line-height: 1.2;color: #333;}
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
         .logo img { width:80px; height:80px; object-fit:contain; }
          .address {font-size: 11px;margin: 8px 0 5px 0;color: #333;}
        .school-title {font-size: 11px;font-weight: bold;margin: 5px 0 0 0;color: #333;}
        .content {padding: 20px 30px 30px 30px;}
        .section-title {font-size: 16px;font-weight: bold;margin-bottom: 15px;border-bottom: 1px solid #000;padding-bottom: 3px;}
        .info-table {width: 100%;margin-bottom: 20px;border-collapse: collapse;}
        .info-table td {padding: 3px 8px 3px 0;font-size: 12px;vertical-align: top;}
        .info-table .label {width: 180px;text-align: right;padding-right: 15px;color: #333;}
        .info-table .value {font-weight: bold;color: #000;}
        .login-info {margin: 15px 0 20px 0;font-size: 11px;line-height: 1.3;}
        .login-info a {color: #0066cc;text-decoration: none;}
        .login-info a:hover {text-decoration: underline;}
        .payment-title {font-size: 14px;font-weight: bold;margin-bottom: 10px;border-bottom: 1px solid #000;padding-bottom: 3px;}
        .payment-intro {font-size: 11px;line-height: 1.4;margin-bottom: 8px;}
        .payment-note {font-size: 11px;line-height: 1.4;margin-bottom: 10px;}
        .payment-steps {margin: 10px 0;}
        .payment-steps ol {padding-left: 16px;margin: 0;}
        .payment-steps li {margin: 5px 0;font-size: 11px;line-height: 1.4;}
        .bank-details {font-size: 11px;line-height: 1.4;margin: 15px 0;}
        .account-number {font-weight: bold;margin: 8px 0;}
        .signature {text-align: right;margin-top: 30px;font-size: 11px;font-weight: bold;}
        @media print {body {background: white;padding: 0;} .container {box-shadow: none;border: 1px solid #000;}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="date-info">
                Created: {{ $createdAt }}<br>
                Cetak: {{ $cetak }}
            </div>
          <div class="logo"><img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}"></div>
                
            <div class="address">{{ config('client.region') }}</div>
            <div class="school-title">PANITIA PENERIMAAN SISWA BARU PSB {{ $peserta->fakultas }}</div>
        </div>
        <div class="content">
            <div class="section-title">Informasi Akun Anda</div>
            <table class="info-table">
                <tr><td class="label">Nama Calon Siswa</td><td class="value">{{ $peserta->nama_peserta }}</td></tr>
                <tr><td class="label">Nomor pendaftaran (Username)</td><td class="value">{{ $peserta->no_pendaftaran }}</td></tr>
                <tr><td class="label">Password</td><td class="value">{{ $user->plain_password }}</td></tr>
                <tr><td class="label">Virtual Akun</td><td class="value">{{ $peserta->va_number }}</td></tr>
                <tr><td class="label">Sekolah</td><td class="value">{{ $peserta->fakultas ?? '-' }}</td></tr>
                <tr><td class="label">Jurusan</td><td class="value">{{ $peserta->prodi ?? '-' }}</td></tr>
                <tr><td class="label">Jenis Pendaftaran</td><td class="value">{{ $peserta->jalur ?? '-' }}</td></tr>
            </table>
            <div class="login-info">
                Gunakan Nomor pendaftaran (Username) dan password untuk login pada URL di bawah ini<br>
                <a href="{{ url('ServiceLogin') }}" class="no-underline text-pmb-green-600 hover:text-pmb-green-700" target="_blank">
    {{ parse_url(url('/'), PHP_URL_HOST) }}/ServiceLogin
</a>
 </div>
            <div class="payment-title">Petunjuk Pembayaran</div>
            <div class="payment-intro">
                <strong>Biaya Pendaftaran sebesar Rp. {{ number_format($biayaPendaftaran,0,',','.') }},- dibayarkan non tunai (langsung ke nomor virtual akun diatas) dengan cara berikut ini :</strong>
            </div>
            <div class="payment-note">
                Pada dasarnya pembayaran biaya Pendaftaran dilakukan dengan 2 cara :
            </div>
            <div class="payment-steps">
                <ol>
                    <li>Transaksi ke bank Muamalat baik teller, ATM, iBanking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transaksi virtual akun.</li>
                    <li>Transaksi ke bank lain (antar bank) baik teller, ATM, iBanking maupun sms banking, maka transaksinya (menu yang dipilih) adalah transfer antar bank.</li>
                </ol>
            </div>
            <div class="bank-details">
                Untuk transaksi antar bank (teller, ATM, iBanking, dll) bank biasanya membutuhkan kode bank tujuan (bank Muamalat), sehingga cara penulisan nomor rekening adalah : kode bank diikuti nomor virtual akun)
            </div>
            <div class="account-number">
                Sebagai berikut : {{ $peserta->va_number }}
            </div>
            <div class="signature">
                Panitia PSB
            </div>
        </div>
    </div>
</body>
</html>
