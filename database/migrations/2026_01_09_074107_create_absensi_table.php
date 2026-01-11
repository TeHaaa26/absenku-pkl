<?php
// database/migrations/2024_01_01_000005_create_absensi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            
            // Data Absen Masuk
            $table->time('jam_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->decimal('jarak_masuk', 10, 2)->nullable()->comment('dalam meter');
            
            // Data Absen Pulang
            $table->time('jam_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->decimal('latitude_pulang', 10, 8)->nullable();
            $table->decimal('longitude_pulang', 11, 8)->nullable();
            $table->decimal('jarak_pulang', 10, 2)->nullable()->comment('dalam meter');
            
            // Status & Keterangan
            $table->enum('status', [
                'hadir', 
                'terlambat', 
                'alpha', 
                'izin_sakit', 
                'izin_dinas',
                'libur'
            ])->default('alpha');
            $table->integer('terlambat_menit')->default(0);
            $table->text('keterangan')->nullable();
            
            $table->timestamps();

            $table->unique(['user_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};