<?php
// app/Models/LokasiSekolah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiSekolah extends Model
{
    protected $table = 'lokasi_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'latitude',
        'longitude',
        'radius',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
    ];
}