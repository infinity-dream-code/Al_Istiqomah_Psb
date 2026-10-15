<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pendaftar Keseluruhan {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:20px; background:#f5f5f5; }
        .container { background:white; max-width:800px; margin:0 auto; padding:30px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        .header { text-align:center; margin-bottom:30px; }
        .logo { width:80px; height:80px; margin:0 auto 15px; display:flex; align-items:center; justify-content:center; }
        .logo img { width:80px; height:80px; object-fit:contain; }
        .school-name { font-size:18px; font-weight:bold; color:#2E7D32; margin:10px 0; }
        .address { font-size:12px; color:#666; margin-bottom:20px; }
        .date-print { text-align:right; font-size:14px; margin-bottom:20px; }
        .title-section { text-align:right; font-size:14px; margin-bottom:30px; }
        table { width:100%; border-collapse:collapse; font-size:12px; }
        th, td { border:1px solid #333; padding:8px 4px; text-align:center; }
        th { background:#f8f9fa; font-weight:bold; }
        .footer-info { margin-top:40px; font-size:12px; }
        .page-break { page-break-before: always; }
        .page-indicator { text-align:right; font-size:12px; margin-top:10px; }
    </style>
</head>
<body>

@foreach($chunks as $page => $list)
<div class="container {{ $page > 0 ? 'page-break' : '' }}">
    <div class="header">
        <div class="logo"><img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}"></div>
        <div class="school-name">PSB {{ config('client.name') }}</div>
        <div class="address">{{ config('client.region') }}</div>
    </div>

    <div class="date-print">Cetak: {{ $today }}</div>
    <div class="title-section">Daftar Pendaftar Keseluruhan<br>{{ $tahun }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama</th>
                <th>No Tlpn/HP</th>
                <th>Jurusan</th>
                <th>Kab/Kota</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($list as $i => $p)
            <tr>
                <td>{{ $page*17 + $i + 1 }}</td>
                <td>{{ $p->no_pendaftaran }}</td>
                <td>{{ $p->nama_peserta }}</td>
                <td>{{ $p->no_hp }}</td>
                <td>{{ $p->prodi }}</td>
                <td>{{ $p->kabupaten }}</td>
                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-indicator">Halaman {{ $page+1 }}/{{ $chunks->count() }}</div>

    @if($page+1 == $chunks->count())
    <div class="footer-info">
        <div style="text-align:left; float:left;">
            <div>{{ config('client.city') }}, {{ $today }}</div>
            <div>Petugas PSB</div>
            <div style="margin-top:80px;"><strong>Admin</strong></div>
        </div>
        <div style="text-align:right; float:right;">
            <div style="margin-top:30px;">Kepala</div>
            <div style="margin-top:60px; border-top:1px dotted #333; width:150px; margin-left:auto;"></div>
        </div>
        <div style="clear:both;"></div>
    </div>
    @endif
</div>
@endforeach

</body>
</html>
