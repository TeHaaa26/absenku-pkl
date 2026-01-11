<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Aplikasi AbsenKu
    |--------------------------------------------------------------------------
    */

    'nama_aplikasi' => env('APP_NAME', 'AbsenKu'),

    // Pengaturan Jam Kerja
    'jam_kerja' => [
        'jam_masuk' => env('ABSENKU_JAM_MASUK', '06:30'),
        'jam_pulang' => env('ABSENKU_JAM_PULANG', '15:00'),
        'batas_absen_masuk' => env('ABSENKU_BATAS_ABSEN_MASUK', '12:00'),
        'batas_absen_pulang' => env('ABSENKU_BATAS_ABSEN_PULANG', '22:00'),
    ],

    // Pengaturan Lokasi
    'lokasi' => [
        'default_radius' => env('ABSENKU_DEFAULT_RADIUS', 100), // dalam meter
    ],

    // Status Absensi
    'status' => [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'alpha' => 'Alpha',
        'izin_sakit' => 'Izin Sakit',
        'izin_dinas' => 'Izin Dinas',
        'libur' => 'Libur',
    ],

    // Warna Status (untuk UI)
    'warna_status' => [
        'hadir' => 'green',
        'terlambat' => 'yellow',
        'alpha' => 'red',
        'izin_sakit' => 'blue',
        'izin_dinas' => 'purple',
        'libur' => 'gray',
    ],
];