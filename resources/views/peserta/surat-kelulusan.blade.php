<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan {{ $status === 'lulus' ? 'Lulus' : 'Tidak Lulus' }} - SPMB {{ config('client.name') }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 0; padding: 20px; background: #f5f5f5; line-height: 1.5; }
        .container { background: #fff; max-width: 800px; margin: 0 auto; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,.08); }
        .header { text-align: center; border-bottom: 3px solid #2E7D32; padding-bottom: 16px; margin-bottom: 28px; }
        .logo img { width: 80px; height: 80px; object-fit: contain; }
        .school-title { font-size: 16px; font-weight: bold; margin-top: 10px; }
        .school-name { font-size: 18px; font-weight: bold; color: #2E7D32; text-transform: uppercase; }
        .address { font-size: 12px; color: #666; }
        .doc-title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin: 28px 0 8px; }
        .doc-sub { text-align: center; margin-bottom: 24px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .label { width: 180px; }
        .info-table .colon { width: 16px; }
        .result { text-align: center; font-size: 20px; font-weight: bold; margin: 24px 0; padding: 12px; border: 2px solid {{ $status === 'lulus' ? '#2E7D32' : '#b91c1c' }}; color: {{ $status === 'lulus' ? '#2E7D32' : '#b91c1c' }}; }
        .body-text { text-align: justify; margin-bottom: 16px; }
        .signature { margin-top: 48px; text-align: right; }
        .signature-space { height: 70px; }
        @media print { body { background: #fff; padding: 0; } .container { box-shadow: none; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><img src="{{ asset(config('client.logo')) }}" alt="Logo"></div>
        <div class="school-title">PANITIA PENERIMAAN SISWA BARU (SPMB)</div>
        <div class="school-name">{{ $peserta->fakultas ?: config('client.name') }}</div>
        <div class="address">{{ config('client.region') }}</div>
    </div>

    <div class="doc-title">SURAT KETERANGAN</div>
    <div class="doc-sub">Hasil Seleksi Penerimaan Siswa Baru</div>

    <p class="body-text">
        Panitia SPMB {{ $peserta->fakultas ?: config('client.name') }} dengan ini menerangkan bahwa:
    </p>

    <table class="info-table">
        <tr><td class="label">Nama</td><td class="colon">:</td><td><strong>{{ $peserta->nama_peserta }}</strong></td></tr>
        <tr><td class="label">No. Pendaftaran</td><td class="colon">:</td><td>{{ $peserta->no_pendaftaran }}</td></tr>
        <tr><td class="label">Virtual Akun</td><td class="colon">:</td><td>{{ $peserta->va_number ?? '-' }}</td></tr>
        <tr><td class="label">Sekolah</td><td class="colon">:</td><td>{{ $peserta->fakultas ?? '-' }}</td></tr>
        <tr><td class="label">Jalur</td><td class="colon">:</td><td>{{ $peserta->jalur ?? '-' }}</td></tr>
        <tr><td class="label">Tahun Akademik</td><td class="colon">:</td><td>{{ $tahun_akademik }}</td></tr>
    </table>

    <div class="result">
        {{ $status === 'lulus' ? 'DINYATAKAN LULUS' : 'DINYATAKAN TIDAK LULUS' }}
    </div>

    <p class="body-text">
        @if($status === 'lulus')
            Yang bersangkutan dinyatakan lulus seleksi SPMB dan dapat melanjutkan proses registrasi / daftar ulang sesuai ketentuan panitia.
        @else
            Yang bersangkutan dinyatakan tidak lulus seleksi SPMB pada periode ini. Keputusan panitia bersifat mutlak.
        @endif
    </p>

    <div class="signature">
        <div>{{ config('client.city') }}, {{ $tanggalSekarang }}</div>
        <div>Panitia SPMB</div>
        <div class="signature-space"></div>
        <div><strong>Ketua Panitia</strong></div>
    </div>
</div>
<script>window.print();</script>
</body>
</html>
