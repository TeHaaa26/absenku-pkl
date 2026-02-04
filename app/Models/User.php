<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nisn', // Ubah dari nip ke nisn
        'nama',
        'email',
        'password',
        'role',
        'lokasi_id',
        'foto_profil',
        'no_telepon',
        'alamat',
        'jenis_kelamin',
        'jurusan', // Ubah dari jabatan ke kelas
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    // Accessors
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getIsSiswaAttribute(): bool
    {
        return $this->role === 'siswa';
    }

    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil) {
            return asset('storage/' . $this->foto_profil);
        }
        return asset('images/default-avatar.png');
    }

    public function getInisialAttribute(): string
    {
        $words = explode(' ', $this->nama);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }

    // Scopes
    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiPkl::class, 'lokasi_id');
    }

    public function getNamaLokasiAttribute(): string
    {
        return $this->lokasi?->nama_tempat_pkl ?? 'Lokasi Belum Diatur';
    }

    public function penempatan()
    {
        return $this->hasOne(PenempatanPkl::class, 'siswa_id');
    }
}
