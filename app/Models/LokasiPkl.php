<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiPkl extends Model
{
    use HasFactory;

    protected $table = 'lokasi_pkl';

    protected $fillable = [
        'nama_tempat_pkl',
        'alamat',
        'latitude',
        'longitude',
        'radius',
        'jam_kerja_id', // Pastikan ini sudah masuk fillable
    ];

    /**
     * Relasi ke model JamKerja
     * Satu lokasi memiliki satu jam kerja tertentu
     */
    // app/Models/LokasiSekolah.php

    public function jamKerja()
    {
        return $this->belongsTo(JamKerja::class, 'jam_kerja_id');
    }

    public function penempatan()
    {
        return $this->hasMany(PenempatanPkl::class, 'lokasi_id');
    }

    
}
