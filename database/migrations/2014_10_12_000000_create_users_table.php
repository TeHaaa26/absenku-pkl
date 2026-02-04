<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Menggunakan NISN karena ini tabel siswa
            $table->string('nisn', 20)->unique(); 
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            
            // Role hanya Admin dan Siswa (Guru nanti saja)
            $table->enum('role', ['admin', 'siswa'])->default('siswa');
            
            $table->string('foto_profil')->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            
            // Menggunakan jurusan untuk siswa
            $table->string('jurusan', 50)->nullable(); 
            
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('email_verified_at')->nullable();
            
            // Relasi ke lokasi (jika diperlukan untuk absen)
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi_pkl')->onDelete('set null');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk mempercepat pencarian login
            $table->index('nisn');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};