<?php
// database/migrations/2024_01_01_000002_create_gurus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('nama', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('jabatan', 100)->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            // Tambahkan dua kolom ini:
            $table->string('status')->default('aktif');
            $table->string('foto_profil')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
