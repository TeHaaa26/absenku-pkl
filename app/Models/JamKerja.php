<?php
// app/Models/JamKerja.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    protected $table = 'jam_kerja';

    protected $fillable = [
        'jam_masuk',
        'jam_pulang',
        'batas_absen_masuk',
        'batas_absen_pulang',
    ];
}