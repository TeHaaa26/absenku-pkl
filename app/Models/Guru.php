<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahkan ini

class Guru extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'password',
        'jabatan',
        'no_telepon',
        'alamat',
        'jenis_kelamin',
        'status', // <--- Pastikan ini ada
        'foto_profil' // <--- Pastikan ini juga ada
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // --- RELASI ---

    /**
     * Menghubungkan Guru dengan tabel PenempatanPkl
     */
    public function penempatan(): HasMany
    {
        // Parameter kedua adalah foreign key di tabel penempatan_pkl
        return $this->hasMany(PenempatanPkl::class, 'guru_id');
    }

    // --- ACCESSOR ---

    public function getInisialAttribute(): string
    {
        $words = explode(' ', $this->nama);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}
