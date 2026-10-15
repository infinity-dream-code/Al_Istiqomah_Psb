<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Formulir PSB {{ $peserta->fakultas ?? '-' }}</title>
     <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background: #f5f5f5;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .page {
            background: white;
            max-width: 800px;
            margin: 0 auto 20px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 1000px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
           
             display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
       
        
        .form-title {
            position: absolute;
            top: 5px;
            right: 10px;
            border: 2px solid #000;
            padding: 5px 15px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .school-info {
            margin: 10px 0;
        }
        
        .school-title {
            font-size: 16px;
            font-weight: bold;
            color: #2E7D32;
            margin: 5px 0;
        }
        
        .school-year {
            font-size: 12px;
            font-weight: bold;
        }
        
        .section {
            margin: 20px 0;
        }
        
        .section-title {
            background: #2E7D32;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .personal-info {
            display: flex;
            gap: 20px;
        }
        
        .photo-section {
            width: 120px;
            flex-shrink: 0;
        }
        
        .photo {
            width: 90px;
            height: 120px;
            border: 1px solid #333;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 10px;
            text-align: center;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTAiIGhlaWdodD0iMTIwIiB2aWV3Qm94PSIwIDAgOTAgMTIwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iOTAiIGhlaWdodD0iMTIwIiBmaWxsPSIjRjBGMEYwIi8+CjxjaXJjbGUgY3g9IjQ1IiBjeT0iMzUiIHI9IjE1IiBmaWxsPSIjQ0NDIi8+CjxwYXRoIGQ9Ik0yMCA4MEMyMCA3MCAzMCA2MCA0NSA2MEM2MCA2MCA3MCA3MCA3MCA4MEw3MCAxMDBMMjAgMTAwWiIgZmlsbD0iI0NDQyIvPgo8L3N2Zz4K');
            background-size: cover;
        }
        
        .info-section {
            flex: 1;
        }
        
        .info-row {
            display: flex;
            margin: 3px 0;
            align-items: flex-start;
        }
        
        .info-label {
            width: 120px;
            font-size: 10px;
            flex-shrink: 0;
        }
        
        .info-colon {
            width: 15px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
            font-weight: bold;
            font-size: 10px;
        }
        
        .parent-info {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        
        .parent-column {
            flex: 1;
        }
        
        .parent-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .school-data {
            margin: 15px 0;
        }
        
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .cost-table th,
        .cost-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .cost-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .cost-table .number-col {
            width: 40px;
            text-align: center;
        }
        
        .cost-table .amount-col {
            width: 100px;
            text-align: right;
        }
        
        .document-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .document-table th,
        .document-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .document-table th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .document-table .check-col {
            width: 40px;
            text-align: center;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 20px 0;
        }
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

        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px dotted #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .date-signature {
            text-align: right;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .cost-section {
            margin-top: 30px;
        }
        
        .total-row {
            font-weight: bold;
            background: #f8f9fa;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .page {
                box-shadow: none;
                margin: 0;
                padding: 15px;
                page-break-after: always;
            }
            
            .page:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="form-title">FORMULIR</div>
 <div class="logo"><img src="{{ asset(config('client.logo')) }}" alt="#"></div>
            
            <div class="school-info">
                <div class="school-title">PENERIMAAN SISWA BARU</div>
                <div class="school-year">{{ $peserta->fakultas ?? '-' }} {{ $peserta->relasiGelombang->tahun_akademik ?? '-' }}</div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">IDENTITAS PERSONAL</div>
            <div class="personal-info">
                <div class="photo-section">
                    <div class="photo"><img src="{{ asset('storage/'.$peserta->foto) }}" 
     alt="Foto 3x4" 
     style="width:90px; height:120px; object-fit:cover;">
</div>
                </div>
                <div class="info-section">
                    <div class="info-row"><div class="info-label">No. Pendaftaran</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->no_pendaftaran }}</div></div>
                    <div class="info-row"><div class="info-label">Nama Peserta</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->nama_peserta }}</div></div>
                    <div class="info-row"><div class="info-label">Asal Sekolah</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->nama_sekolah ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Alamat</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->alamat_lengkap ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. KK, NIK</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->no_kk ?? '-' }}, {{ $peserta->nik ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. Telepon</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->no_hp ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Kecamatan</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->kecamatan ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Kota</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->kabupaten ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Kode Pos</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->kode_pos ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. Akta Lahir</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->no_akta_lahir ?? '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">IDENTITAS ORANG TUA/WALI</div>
            <div class="parent-info">
                <div class="parent-column">
                    <div class="parent-title">Nama Ibu</div>
                    <div class="info-row"><div class="info-label">Tanggal Lahir</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_tanggal_lahir ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">NIK</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_nik ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Suku</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_suku ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Pekerjaan Ibu</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_pekerjaan ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. Telepon</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_no_tlp ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Penghasilan</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_penghasilan ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Pendidikan</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ibu_pendidikan ?? '-' }}</div></div>
                </div>
                <div class="parent-column">
                    <div class="parent-title">Nama Ayah</div>
                    <div class="info-row"><div class="info-label">Tanggal Lahir</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_tanggal_lahir ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">NIK</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_nik ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Suku</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_suku ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Pekerjaan Ayah</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_pekerjaan ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">No. Telepon</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_no_tlp ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Penghasilan</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_penghasilan ?? '-' }}</div></div>
                    <div class="info-row"><div class="info-label">Pendidikan</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->ayah_pendidikan ?? '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">DATA SEKOLAH</div>
            <div class="school-data">
                <div class="info-row"><div class="info-label">Nama Sekolah</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->nama_sekolah ?? '-' }}</div></div>
                <div class="info-row"><div class="info-label">Kota Sekolah</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->kota_sekolah ?? '-' }}</div></div>
                <div class="info-row"><div class="info-label">Provinsi</div><div class="info-colon">:</div><div class="info-value">{{ $peserta->provinsi_sekolah ?? '-' }}</div></div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Biaya Registrasi dan Jadwal ujian</div>
            <table class="cost-table">
                <thead><tr><th class="number-col">No</th><th>Pilihan</th><th>Keterangan</th></tr></thead>
                <tbody>
                    <tr><td class="number-col">1</td><td>Biaya Pendaftaran</td><td>Rp. {{ number_format($biayaPendaftaran,0,',','.') }}</td></tr>
                    @foreach($ujian as $i => $u)
                        <tr><td class="number-col">{{ $i+2 }}</td><td>{{ $u->masterUjian->nama ?? '-' }}</td><td>{{ $u->tanggal ?? '-' }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="signature-section">
            <div class="signature-box"><div>Orang Tua/Wali</div><div class="signature-line">......................................</div></div>
            <div class="signature-box"><div class="date-signature">......................., {{ $tanggalSekarang }}</div><div class="signature-line">{{ $peserta->nama_peserta }}</div></div>
        </div>
        <div class="section">
            <div class="section-title">Daftar Berkas</div>
            <table class="document-table">
                <thead><tr><th class="check-col">No</th><th class="check-col">Cek</th><th>Nama Dokumen</th><th>Keterangan</th></tr></thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach($berkas as $label => $val)
                        <tr>
                            <td class="check-col">{{ $i++ }}</td>
                            <td class="check-col"></td>
                            <td>{{ $label }}</td>
                            <td>{{ $val ? 'Sudah Upload' : 'Belum' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="page page-break">
        <div class="header">
            <div class="form-title">FORMULIR</div>
            <div class="logo"></div>
            <div class="school-info">
                <div class="school-title">PENERIMAAN SISWA BARU</div>
                <div class="school-year">PSB {{ $peserta->fakultas ?? '-' }} {{ $peserta->gelombang ?? '-' }}</div>
            </div>
        </div>
        <div class="section cost-section">
            <div class="section-title">Rincian Biaya Daftar Ulang (Jika diterima)</div>
            <table class="cost-table">
                <thead><tr><th class="number-col">#</th><th>Biaya Masuk Siswa Baru</th><th class="amount-col">Nominal</th><th>Keterangan</th></tr></thead>
               <tbody>
    @php $total=0; @endphp
    @foreach($detailBiaya as $i => $row)
        <tr>
            <td class="number-col">{{ $i+1 }}</td>
            <td>{{ $row['nama_tagihan'] ?? '-' }}</td>
            <td class="amount-col">{{ number_format((int)($row['biaya'] ?? 0),0,',','.') }}</td>
            <td>{{ $row['no_akun'] ?? '-' }}</td>
        </tr>
        @php $total += (int)($row['biaya'] ?? 0); @endphp
    @endforeach
    <tr class="total-row">
        <td colspan="2" style="text-align: center;">Total</td>
        <td class="amount-col">{{ number_format($total,0,',','.') }}</td>
        <td></td>
    </tr>
</tbody>

            </table>
        </div>
    </div>
</body>
</html>
