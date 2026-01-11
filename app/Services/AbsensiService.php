<?php
// app/Services/AbsensiService.php

namespace App\Services;

use App\Models\Absensi;
use App\Models\LokasiSekolah;
use App\Models\JamKerja;
use App\Models\HariLibur;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiService
{
    /**
     * Cek apakah hari ini adalah hari libur
     */
    public function isHariLibur(?Carbon $tanggal = null): bool
    {
        $tanggal = $tanggal ?? Carbon::today();
        
        // Cek hari Minggu
        if ($tanggal->dayOfWeek === Carbon::SUNDAY) {
            return true;
        }
        
        // Cek di tabel hari libur
        return HariLibur::whereDate('tanggal', $tanggal)->exists();
    }

    /**
     * Get keterangan hari libur
     */
    public function getKeteranganLibur(?Carbon $tanggal = null): ?string
    {
        $tanggal = $tanggal ?? Carbon::today();
        
        if ($tanggal->dayOfWeek === Carbon::SUNDAY) {
            return 'Hari Minggu';
        }
        
        $libur = HariLibur::whereDate('tanggal', $tanggal)->first();
        return $libur?->keterangan;
    }

    /**
     * Hitung jarak antara 2 koordinat (Haversine formula)
     */
    public function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Validasi apakah dalam radius sekolah
     */
    public function validasiLokasi(float $latitude, float $longitude): array
    {
        $lokasi = LokasiSekolah::first();
        
        if (!$lokasi) {
            return [
                'valid' => false,
                'message' => 'Lokasi sekolah belum diatur. Hubungi admin.',
                'jarak' => 0,
            ];
        }

        $jarak = $this->hitungJarak(
            $latitude, 
            $longitude, 
            $lokasi->latitude, 
            $lokasi->longitude
        );

        $dalamRadius = $jarak <= $lokasi->radius;

        return [
            'valid' => $dalamRadius,
            'message' => $dalamRadius 
                ? "Anda dalam radius sekolah ({$jarak}m)" 
                : "Anda di luar radius sekolah ({$jarak}m dari {$lokasi->radius}m)",
            'jarak' => $jarak,
            'radius' => $lokasi->radius,
        ];
    }

    /**
     * Proses absen masuk
     */
    public function absenMasuk(User $user, array $data): array
    {
        // Cek hari libur
        if ($this->isHariLibur()) {
            return [
                'success' => false,
                'message' => 'Hari ini adalah ' . $this->getKeteranganLibur() . '. Tidak perlu absen.',
            ];
        }

        // Validasi lokasi
        $validasiLokasi = $this->validasiLokasi($data['latitude'], $data['longitude']);
        if (!$validasiLokasi['valid']) {
            return [
                'success' => false,
                'message' => $validasiLokasi['message'],
            ];
        }

        // Cek jam kerja
        $jamKerja = JamKerja::first();
        $now = Carbon::now();
        $batasAbsenMasuk = Carbon::parse($jamKerja->batas_absen_masuk);

        if ($now->gt($batasAbsenMasuk)) {
            return [
                'success' => false,
                'message' => 'Batas waktu absen masuk sudah lewat (' . $jamKerja->batas_absen_masuk . ')',
            ];
        }

        // Cek sudah absen atau belum
        $absensi = Absensi::where('user_id', $user->id)
                         ->whereDate('tanggal', Carbon::today())
                         ->first();

        if ($absensi && $absensi->jam_masuk) {
            return [
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini pada ' . $absensi->jam_masuk,
            ];
        }

        // Hitung keterlambatan
        $jamMasuk = Carbon::parse($jamKerja->jam_masuk);
        $terlambatMenit = 0;
        $status = 'hadir';

        if ($now->gt($jamMasuk)) {
            $terlambatMenit = $now->diffInMinutes($jamMasuk);
            $status = 'terlambat';
        }

        // Simpan foto
        $fotoPath = $this->simpanFoto($data['foto'], $user->id, 'masuk');

        // Simpan atau update absensi
        $absensi = Absensi::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => Carbon::today(),
            ],
            [
                'jam_masuk' => $now->format('H:i:s'),
                'foto_masuk' => $fotoPath,
                'latitude_masuk' => $data['latitude'],
                'longitude_masuk' => $data['longitude'],
                'jarak_masuk' => $validasiLokasi['jarak'],
                'status' => $status,
                'terlambat_menit' => $terlambatMenit,
            ]
        );

        $message = 'Absen masuk berhasil pada ' . $now->format('H:i');
        if ($status === 'terlambat') {
            $message .= '. Anda terlambat ' . $absensi->terlambat_format;
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $absensi,
        ];
    }

    /**
     * Proses absen pulang
     */
    public function absenPulang(User $user, array $data): array
    {
        // Cek hari libur
        if ($this->isHariLibur()) {
            return [
                'success' => false,
                'message' => 'Hari ini adalah hari libur. Tidak perlu absen.',
            ];
        }

        // Cek sudah absen masuk atau belum
        $absensi = Absensi::where('user_id', $user->id)
                         ->whereDate('tanggal', Carbon::today())
                         ->first();

        if (!$absensi || !$absensi->jam_masuk) {
            return [
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini.',
            ];
        }

        if ($absensi->jam_pulang) {
            return [
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini pada ' . $absensi->jam_pulang,
            ];
        }

        // Validasi lokasi
        $validasiLokasi = $this->validasiLokasi($data['latitude'], $data['longitude']);
        if (!$validasiLokasi['valid']) {
            return [
                'success' => false,
                'message' => $validasiLokasi['message'],
            ];
        }

        // Cek jam kerja
        $jamKerja = JamKerja::first();
        $now = Carbon::now();
        $batasAbsenPulang = Carbon::parse($jamKerja->batas_absen_pulang);

        if ($now->gt($batasAbsenPulang)) {
            return [
                'success' => false,
                'message' => 'Batas waktu absen pulang sudah lewat (' . $jamKerja->batas_absen_pulang . ')',
            ];
        }

        // Simpan foto
        $fotoPath = $this->simpanFoto($data['foto'], $user->id, 'pulang');

        // Update absensi
        $absensi->update([
            'jam_pulang' => $now->format('H:i:s'),
            'foto_pulang' => $fotoPath,
            'latitude_pulang' => $data['latitude'],
            'longitude_pulang' => $data['longitude'],
            'jarak_pulang' => $validasiLokasi['jarak'],
        ]);

        return [
            'success' => true,
            'message' => 'Absen pulang berhasil pada ' . $now->format('H:i'),
            'data' => $absensi,
        ];
    }

    /**
     * Simpan foto dari base64
     */
    private function simpanFoto(string $base64, int $userId, string $tipe): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image = base64_decode($base64);

        $folder = "absensi/{$userId}/" . date('Y-m');
        $filename = "{$tipe}_" . date('Y-m-d_His') . '.jpg';
        $path = "{$folder}/{$filename}";

        Storage::disk('public')->put($path, $image);

        return $path;
    }

    /**
     * Get status absensi hari ini
     */
    public function getStatusHariIni(User $user): array
    {
        $today = Carbon::today();
        $jamKerja = JamKerja::first();
        
        // Cek hari libur
        if ($this->isHariLibur($today)) {
            return [
                'tanggal' => $today->format('Y-m-d'),
                'hari' => $today->isoFormat('dddd'),
                'tanggal_lengkap' => $today->isoFormat('dddd, D MMMM Y'),
                'is_libur' => true,
                'keterangan_libur' => $this->getKeteranganLibur($today),
                'absensi' => null,
                'jam_kerja' => $jamKerja,
            ];
        }

        $absensi = Absensi::where('user_id', $user->id)
                         ->whereDate('tanggal', $today)
                         ->first();

        return [
            'tanggal' => $today->format('Y-m-d'),
            'hari' => $today->isoFormat('dddd'),
            'tanggal_lengkap' => $today->isoFormat('dddd, D MMMM Y'),
            'is_libur' => false,
            'keterangan_libur' => null,
            'absensi' => $absensi,
            'jam_kerja' => $jamKerja,
            'sudah_absen_masuk' => $absensi && $absensi->jam_masuk,
            'sudah_absen_pulang' => $absensi && $absensi->jam_pulang,
        ];
    }

    /**
     * Get rekap absensi bulanan
     */
    public function getRekapBulanan(User $user, int $bulan, int $tahun): array
    {
        $absensi = Absensi::where('user_id', $user->id)
                         ->byBulan($bulan, $tahun)
                         ->get();

        $totalHariKerja = $this->hitungHariKerja($bulan, $tahun);

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_hari_kerja' => $totalHariKerja,
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'terlambat' => $absensi->where('status', 'terlambat')->count(),
            'alpha' => $totalHariKerja - $absensi->whereIn('status', ['hadir', 'terlambat', 'izin_sakit', 'izin_dinas'])->count(),
            'izin_sakit' => $absensi->where('status', 'izin_sakit')->count(),
            'izin_dinas' => $absensi->where('status', 'izin_dinas')->count(),
            'total_terlambat_menit' => $absensi->sum('terlambat_menit'),
        ];
    }

    /**
     * Hitung jumlah hari kerja dalam sebulan
     */
    private function hitungHariKerja(int $bulan, int $tahun): int
    {
        $start = Carbon::createFromDate($tahun, $bulan, 1);
        $end = $start->copy()->endOfMonth();
        $today = Carbon::today();

        // Jika bulan yang dihitung adalah bulan ini, hitung sampai hari ini saja
        if ($end->gt($today)) {
            $end = $today;
        }

        $hariKerja = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!$this->isHariLibur($current)) {
                $hariKerja++;
            }
            $current->addDay();
        }

        return $hariKerja;
    }
}