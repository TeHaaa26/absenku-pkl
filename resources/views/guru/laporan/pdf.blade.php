<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        /* Header Styling */
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            text-transform: uppercase;
            margin: 0;
            font-size: 16px;
            color: #000;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        /* Information Section */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            border: none;
            text-align: left;
            padding: 2px 0;
        }

        /* Main Data Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            border: 1px solid #999;
            padding: 8px 4px;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            border: 1px solid #999;
            padding: 6px 4px;
            text-align: center;
        }

        .text-left {
            text-align: left;
            padding-left: 8px;
        }

        .bg-gray {
            background-color: #f9f9f9;
        }

        /* Footer / Signature Section */
        .footer-container {
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Laporan Rekapitulasi Absensi Siswa PKL</h1>
        <p style="font-size: 14px; font-weight: bold; color: #444; margin-top: 5px;">
            {{ strtoupper($daftarLokasi) }}
        </p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Guru Pembimbing</td>
            <td width="2%">:</td>
            <td><strong>{{ $guruNama }}</strong></td>
            <td width="15%" style="text-align: right;">Periode</td>
            <td width="2%">:</td>
            <td width="20%">{{ $namaBulan }} {{ $tahun }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th width="50">Hadir</th>
                <th width="50">Sakit</th>
                <th width="50">Izin</th>
                <th width="50">Alpha</th>
                <th width="50">Late</th>
                <th>Total Menit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataRekap as $index => $data)
            <tr class="{{ $index % 2 == 1 ? 'bg-gray' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['siswa']->nisn }}</td>
                <td class="text-left">{{ $data['siswa']->nama }}</td>
                <td>{{ $data['rekap']['hadir'] }}</td>
                <td>{{ $data['rekap']['izin_sakit'] }}</td>
                <td>{{ $data['rekap']['izin_dinas'] }}</td>
                <td>{{ $data['rekap']['alpha'] }}</td>
                <td>{{ $data['rekap']['terlambat'] }}</td>
                <td>{{ $data['rekap']['total_terlambat_menit'] }} m</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-container">
        <div class="signature-box">
            <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
            <p>Guru Pembimbing PKL,</p>
            <div class="signature-space"></div>
            <p><strong>( {{ $guruNama }} )</strong></p>
            <p>NIP. ........................................</p>
        </div>
    </div>
    <div class="clear"></div>

</body>

</html>