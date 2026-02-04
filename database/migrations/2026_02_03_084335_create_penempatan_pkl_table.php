<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penempatan_pkl', function (Blueprint $table) {
            $table->id();
            // Relasi ke Siswa (tabel users)
            $table->foreignId('siswa_id')->constrained('users')->onDelete('cascade');
            // Relasi ke Pembimbing (tabel gurus)
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            // Relasi ke Lokasi (tabel lokasi_pkl)
            $table->foreignId('lokasi_id')->constrained('lokasi_pkl')->onDelete('cascade');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penempatan_pkl');
    }
};
