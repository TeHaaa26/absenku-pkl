<?php
// app/Models/Absensi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'jarak_masuk',
        'jam_pulang',
        'foto_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'jarak_pulang',
        'status',
        'terlambat_menit',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_pulang' => 'decimal:8',
        'longitude_pulang' => 'decimal:8',
        'jarak_masuk' => 'decimal:2',
        'jarak_pulang' => 'decimal:2',
        'terlambat_menit' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFotoMasukUrlAttribute(): ?string
    {
        return $this->foto_masuk ? asset('storage/' . $this->foto_masuk) : null;
    }

    public function getFotoPulangUrlAttribute(): ?string
    {
        return $this->foto_pulang ? asset('storage/' . $this->foto_pulang) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'alpha' => 'Alpha',
            'izin_sakit' => 'Izin Sakit',
            'izin_dinas' => 'Izin Dinas',
            'libur' => 'Libur',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'hadir' => 'green',
            'terlambat' => 'yellow',
            'alpha' => 'red',
            'izin_sakit' => 'blue',
            'izin_dinas' => 'purple',
            'libur' => 'gray',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function getTerlambatFormatAttribute(): string
    {
        if ($this->terlambat_menit <= 0) {
            return '-';
        }
        
        $jam = floor($this->terlambat_menit / 60);
        $menit = $this->terlambat_menit % 60;
        
        if ($jam > 0) {
            return "{$jam} jam {$menit} menit";
        }
        return "{$menit} menit";
    }

    // Scopes
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', Carbon::now()->month)
                     ->whereYear('tanggal', Carbon::now()->year);
    }

    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
                     ->whereYear('tanggal', $tahun);
    }
}