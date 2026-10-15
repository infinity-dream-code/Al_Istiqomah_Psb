<!DOCTYPE html>
<html>
<head>
    <title>Kartu Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: top; }
        .logo { width: 70px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; }
        .subtitle { text-align: center; margin-bottom: 10px; }
        .foto { width: 100px; height: 120px; border: 1px solid #000; }
        .data-container { display: flex; gap: 20px; margin: 10px 0; }
        .data-text { flex: 1; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: center; font-size: 11px; }
        .footer { margin-top: 15px; text-align: right; font-weight: bold; }
        .card { width: 100%; }

        
    </style>
</head>
<body>
    @foreach($pesertaList as $index => $peserta)
    <div class="card">
    <table style="border:none; border-collapse:collapse; width:100%; margin-bottom:5px;">
    <tr>
        <td style="border:none;"></td>
         <td style="border:none;"></td>
          <td style="border:none;"></td>
           <td style="border:none;"></td>
            <td style="border:none;"></td>
             <td style="border:none;"></td>
              <td style="border:none;"></td>
               <td style="border:none;"></td>
                <td style="border:none;"></td>
 <td style="border:none;"></td>
   <td style="width: 80px; border:none; vertical-align:middle;">
    <img src="{{ public_path(config('client.logo')) }}" style="width:70px;">
</td>

    <td style="border:none; text-align:left; padding-top:5px; padding-left:20px;">
        <div style="font-size:20px; font-weight:bold;">PENERIMAAN SISWA BARU</div>
        <div style="font-size:15px; font-weight:bold;">
            PSB {{ $peserta->fakultas }} {{ $peserta->relasiGelombang->tahun_akademik ?? '-' }}<br>
            No. Pendaftaran {{ $peserta->no_pendaftaran }}
        </div>
    </td>
</tr>

</table>

<hr style="border:1px solid #000; margin:20px 0 15px 0;">

        
        <div style="text-align:center; font-weight:bold; margin:30px 0;">
            KARTU TANDA PESERTA UJIAN
        </div>
        
    <table style="border:none; margin:10px 0; border-collapse:collapse;">
    <tr>
       <td style="width:110px; vertical-align:top;">
   <img src="{{ public_path('storage/'.$peserta->foto) }}" 
     alt="Foto 3x4" 
     style="width:113px; height:151px;  object-fit:cover;">

</td>

    <td style="border:none; padding-left:30px; vertical-align:middle; text-align:left; font-size:14px;">
    <p><strong>Sekolah:</strong> {{ $peserta->fakultas }}</p>
    <p><strong>NAMA:</strong> {{ $peserta->nama_peserta }}</p>
    <p><strong>ASAL:</strong> {{ $peserta->nama_sekolah ?? '-' }}</p>
</td>

   
    </tr>
</table>


        
        <h4 style="margin-top:50px;">{{ $peserta->prodi }}</h4>
     <table>
    <thead>
        <tr>
            <th>Jadwal Ujian</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Ruang</th>
        </tr>
    </thead>
    <tbody>
        @foreach($peserta->ujian as $ujian)
            <tr>
                <td>{{ $ujian->masterUjian->nama }}</td>
                <td>{{ $ujian->tanggal ? \Carbon\Carbon::parse($ujian->tanggal)->format('d-m-Y') : 'Tidak Ada Ujian' }}</td>
                <td>{{ $ujian->tanggal ? \Carbon\Carbon::parse($ujian->tanggal)->format('H:i') : '-' }}</td>
                <td>{{ $ujian->ruang ?? '-' }}</td>
            </tr>
        @endforeach

        <tr>
            <td>Lokasi</td>
            <td colspan="3"> {{ config('client.city') }} </td>
        </tr>
    </tbody>
</table>

        <h4 style="margin-top:50px;">Ceklist Test</h4>
        <table>
    <thead>
        <tr>
             @foreach($peserta->ujian as $ujian)
                <th>{{ $ujian->masterUjian->nama }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr style="height:80px;">
 @foreach($peserta->ujian as $ujian)
    @php
        $ujian->masterUjian->nama 
    @endphp
    <td style="height:80px;">
    </td>
@endforeach

        </tr>
       
    </tbody>
</table>

        <div class="footer" style="margin-top:20px;">PANITIA PSB</div>
    </div>
    @if($index < count($pesertaList)-1)
        <div style="page-break-after: always;"></div>
    @endif
    @endforeach
</body>
</html>