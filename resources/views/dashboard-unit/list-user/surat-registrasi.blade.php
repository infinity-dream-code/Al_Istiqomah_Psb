<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Penerimaan Siswa Baru - PSB {{ $peserta->fakultas }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            line-height: 1.4;
        }
        .container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            min-height: 900px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2E7D32;
            padding-bottom: 20px;
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
        .logo img { 
            width:80px; 
            height:80px; 
            object-fit:contain; 
        }
        .school-info {
            margin-top: 15px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #2E7D32;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        .school-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }
        .address {
            font-size: 12px;
            color: #666;
            margin: 5px 0;
        }
        .website-email {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
        }
        .document-title {
            text-align: center;
            margin: 30px 0;
        }
        .doc-type {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
        }
        .doc-subtitle {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .doc-detail {
            font-size: 14px;
            font-weight: bold;
            color: #2E7D32;
        }
        .content-section {
            margin: 30px 0;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
            font-size: 13px;
        }
        .info-table .label {
            width: 150px;
            font-weight: normal;
        }
        .info-table .colon {
            width: 20px;
            text-align: center;
        }
        .info-table .value {
            font-weight: normal;
        }
        .decision-section {
            margin: 30px 0;
        }
        .decision-header {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
            text-align: center;
        }
        .decision-item {
            margin: 10px 0;
            display: flex;
        }
        .decision-number {
            width: 80px;
            font-weight: bold;
        }
        .decision-text {
            flex: 1;
            text-align: justify;
        }
        .tagihan-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        .tagihan-table th {
            background-color: #2E7D32;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .tagihan-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        .tagihan-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .tagihan-table .text-center {
            text-align: center;
        }
        .tagihan-table .text-right {
            text-align: right;
        }
        .tagihan-table .total-row {
            background-color: #e8f5e9;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        .signature-left {
            text-align: left;
            font-size: 12px;
        }
        .signature-right {
            text-align: center;
            font-size: 12px;
        }
        .signature-space {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            width: 200px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(46, 125, 50, 0.1);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }
        .content {
            position: relative;
            z-index: 1;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="header">
                <div class="logo"><img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}"></div>
                <div class="school-info">
                    <div class="school-title">PANITIA PENERIMAAN SISWA BARU (PSB)</div>
                    <div class="school-name">{{ $peserta->fakultas }}</div>
                    <div class="address">{{ config('client.region') }}</div>
                    <div class="website-email">Website : {{ url('/') }} || Email :</div>
                </div>
            </div>
            
            <div class="document-title">
                <div class="doc-type">SURAT KEPUTUSAN</div>
                <div class="doc-type" style="text-decoration: none;">PANITIA PENERIMAAN SISWA BARU (PSB)</div>
                <div class="doc-detail">{{ $peserta->fakultas }}</div>
            </div>
            
            <div class="content-section">
                <table class="info-table">
                    <tr>
                        <td class="label">Tahun Pendaftaran</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $tahun_akademik }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Surat</td>
                        <td class="colon">:</td>
                        <td class="value">Rahasia</td>
                    </tr>
                    <tr>
                        <td class="label">Kepada Yth.</td>
                        <td class="colon">:</td>
                        <td class="value">Sdr. Calon Siswa Baru</td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value"><strong>{{ $peserta->nama_peserta }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value">No. Registrasi : <strong>{{ $peserta->no_pendaftaran }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value">No. Virtual Akun <strong>{{ $peserta->va_number }}</strong> (ini adalah nomor rekening ananda)</td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value">Sekolah : <strong>{{ $peserta->fakultas ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value">Jurusan : <strong>{{ $peserta->prodi ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value">Jalur : <strong>{{ $peserta->jalur ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label"></td>
                        <td class="colon"></td>
                        <td class="value"><strong>Di TEMPAT</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="decision-section">
                <div class="decision-header">MEMUTUSKAN</div>
                <div style="display:flex; align-items:flex-start; margin-bottom:8px;">
                    <div style="min-width:80px; font-weight:bold;">Pertama</div>
                    <div style="min-width:10px;">:</div>
                    <div style="flex:1; text-align:justify;">
                        Peserta Penerimaan Siswa Baru PSB {{ $peserta->fakultas }} melalui seleksi test tahun akademik 
                        {{ $tahun_akademik }} dinyatakan <strong>{{ $peserta->status_ujian }}</strong> sebagai Siswa Baru PSB {{ $peserta->fakultas }}
                        sebagaimana tercantum dalam surat keputusan ini.
                    </div>
                </div>
                <div style="display:flex; align-items:flex-start; margin-bottom:8px;">
                    <div style="min-width:80px; font-weight:bold;">Kedua</div>
                    <div style="min-width:10px;">:</div>
                    <div style="flex:1; text-align:justify;">
                        Surat keputusan ini mutlak tidak dapat di ganggu gugat.
                    </div>
                </div>
                <div style="display:flex; align-items:flex-start; margin-bottom:8px;">
                    <div style="min-width:80px; font-weight:bold;">Ketiga</div>
                    <div style="min-width:10px;">:</div>
                    <div style="flex:1; text-align:justify;">
                        Keputusan ini mulai berlaku sejak tanggal di tetapkan dengan ketentuan apabila dikemudian 
                        hari terdapat kekeliruan, maka akan dilakukan perbaikan sebagaimana mestinya.
                    </div>
                </div>
            </div>

            @if($tagihan)
            <div style="margin: 30px 0;">
                <table class="tagihan-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Pembayaran</th>
                            <th class="text-right" style="width: 150px;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $detail = is_string($tagihan->detail) ? json_decode($tagihan->detail, true) : $tagihan->detail;
                            $no = 1;
                        @endphp
                        @if(is_array($detail))
                            @foreach($detail as $item)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $item['nama_tagihan'] ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @endif
                        <tr class="total-row">
                            <td colspan="2" class="text-center">TOTAL PEMBAYARAN</td>
                            <td class="text-right">Rp {{ number_format($tagihan->biaya_daful ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
            
            <div class="signature-section">
                <div class="signature-left">
                    <div>Ttd</div>
                    <div>Calon Siswa</div>
                </div>
                <div class="signature-right">
                    <div>Padang, {{ $tanggalSekarang }}</div>
                    <div>PANITIA PSB</div>
                    <div>PSB {{ $peserta->fakultas }}</div>
                    <div class="signature-space"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>