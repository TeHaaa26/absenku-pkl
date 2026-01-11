<?php
// database/migrations/2024_01_01_000002_create_lokasi_sekolah_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah', 150);
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius')->default(100)->comment('dalam meter');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_sekolah');
    }
};