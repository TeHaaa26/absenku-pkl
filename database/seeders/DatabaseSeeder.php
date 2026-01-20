<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LokasiSekolah;
use App\Models\JamKerja;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Admin
        User::create([
            'nip' => 'ADMIN001',
            'nama' => 'Administrator',
            'email' => 'admin@absenku.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Buat beberapa guru contoh
        User::create([
            'nip' => '198501152010011001',
            'nama' => 'Ahmad Fauzi, S.Pd',
            'email' => 'ahmad.fauzi@absenku.com',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'jenis_kelamin' => 'L',
            'jabatan' => 'Guru Matematika',
            'status' => 'aktif',
        ]);

        User::create([
            'nip' => '199003212015022002',
            'nama' => 'Siti Aminah, S.Pd',
            'email' => 'siti.aminah@absenku.com',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'jenis_kelamin' => 'P',
            'jabatan' => 'Guru Bahasa Indonesia',
            'status' => 'aktif',
        ]);

        User::create([
            'nip' => '198712102012011003',
            'nama' => 'Budi Santoso, M.Pd',
            'email' => 'budi.santoso@absenku.com',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'jenis_kelamin' => 'L',
            'jabatan' => 'Guru Fisika',
            'status' => 'aktif',
        ]);

        // Set Lokasi Sekolah Default (Jakarta sebagai contoh)
        LokasiSekolah::create([
            'nama_sekolah' => 'SMA Negeri 1 Contoh',
            'alamat' => 'Jl. Pendidikan No. 1, Jakarta',
            'latitude' => -6.50670,
            'longitude' => 107.61231,
            // 'latitude' => -6.200000,
            // 'longitude' => 106.816666,
            'radius' => 10000,
        ]);

        // Set Jam Kerja Default
        JamKerja::create([
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:00:00',
            'batas_absen_masuk' => '23:00:00',
            'batas_absen_pulang' => '22:00:00',
        ]);
    }
}