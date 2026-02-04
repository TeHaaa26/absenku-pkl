<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JamKerja extends Model
{
    protected $table = 'jam_kerja';

    // Tambahkan 'nama_shift' agar bisa disimpan ke database
    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'batas_absen_masuk',
        'batas_absen_pulang',
    ];

    /**
     * Relasi ke LokasiPkl
     * Menghubungkan Jam Kerja dengan banyak lokasi (pkl)
     */
    public function lokasis(): HasMany
    {
        // Pastikan nama model dan foreign key-nya sesuai
        return $this->hasMany(LokasiPkl::class, 'jam_kerja_id');
    }
}