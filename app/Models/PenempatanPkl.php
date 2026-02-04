<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenempatanPkl extends Model
{
    use HasFactory;

    // Nama tabel harus sesuai dengan migration
    protected $table = 'penempatan_pkl';

    // Kolom yang boleh diisi lewat form
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'lokasi_id',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    /**
     * Relasi ke Siswa (User)
     */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Relasi ke Guru Pembimbing
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Lokasi PKL
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiPkl::class, 'lokasi_id');
    }
}