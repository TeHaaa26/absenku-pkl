<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guru;
use App\Models\LokasiPkl;
use App\Models\JamKerja;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Set Jam Kerja Default
        JamKerja::create([
            'nama_shift' => 'Shift Pagi Umum', // Tambahkan nama shift jika ada di tabel
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:30:00',
            'batas_absen_masuk' => '09:00:00',
            'batas_absen_pulang' => '20:00:00',
        ]);
        
        JamKerja::create([
            'nama_shift' => 'Shift Instansi',
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '16:00:00',
            'batas_absen_masuk' => '08:30:00',
            'batas_absen_pulang' => '20:00:00',
        ]);

        // 2. Buat Guru Pembimbing
        Guru::create([
            'nip' => '3',
            'nama' => 'Dzikri Pangestu',
            'email' => 'dzikri.pangestu@absenku.com',
            'password' => bcrypt('guru123'),
            'jabatan' => 'Pembimbing PKL',
        ]);
        Guru::create([
            'nip' => '2',
            'nama' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@absenku.com',
            'password' => bcrypt('guru123'),
            'jabatan' => 'Pembimbing PKL',
        ]);

        // 3. Set Lokasi PKL Default
        LokasiPkl::create([
            'nama_tempat_pkl' => 'SMKN Negeri 1 Subang',
            'alamat' => 'Jl. Arief Rahman Hakim No.35, Cigadung',
            'latitude' => -6.55570,
            'longitude' => 107.75980,
            'radius' => 200,
            'jam_kerja_id' => 1
        ]);

        // 4. Buat Admin
        User::create([
            'nisn' => 'ADMIN001',
            'nama' => 'Administrator',
            'email' => 'admin@absenku.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);
        User::create([
            'nisn' => '1',
            'nama' => 'Alifth',
            'email' => 'alifth@absenku.com',
            'password' => Hash::make('siswa123'),
            'role' => 'siswa',
            'status' => 'aktif',
        ]);
        User::create([
            'nisn' => '3',
            'nama' => 'udin',
            'email' => 'udin@absenku.com',
            'password' => Hash::make('siswa123'),
            'role' => 'siswa',
            'status' => 'aktif',
        ]);

        // 5. Buat 100 Siswa Dummy (Menggunakan Model User agar bisa login)
        $jurusan = ['XII RPL 1', 'XII RPL 2', 'XII TKJ 1', 'XII TKJ 2', 'XII MM 1'];

        // for ($i = 1; $i <= 100; $i++) {
        //     User::create([
        //         'nisn' => $faker->unique()->numerify('00########'),
        //         'nama' => $faker->name,
        //         'email' => $faker->unique()->safeEmail,
        //         'password' => Hash::make('siswa123'),
        //         'role' => 'siswa',
        //         'jenis_kelamin' => $faker->randomElement(['L', 'P']),
        //         'jurusan' => $faker->randomElement($jurusan),
        //         'status' => 'aktif',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
    }
}