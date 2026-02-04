<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4f46e5;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-left {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI SISWA</h1>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th class="text-left">NISN</th>
                <th class="text-left">Nama Siswa</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Sakit</th>
                <th>Dinas</th>
                <th>Alpha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataRekap as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $data['siswa']->nisn }}</td>
                <td class="text-left">{{ $data['siswa']->nama }}</td>
                <td>{{ $data['rekap']['hadir'] }}</td>
                <td>{{ $data['rekap']['terlambat'] }}</td>
                <td>{{ $data['rekap']['izin_sakit'] }}</td>
                <td>{{ $data['rekap']['izin_dinas'] }}</td>
                <td>{{ $data['rekap']['alpha'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>........................., {{ now()->format('d') }} {{ now()->translatedFormat('F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br><br>
        <p>_________________________</p>
        <p>NIP. ........................</p>
    </div>
</body>
</html>