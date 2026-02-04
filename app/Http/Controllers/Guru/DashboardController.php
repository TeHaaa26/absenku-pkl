<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin; // Import Model Izin
use App\Models\PenempatanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $hariIni = now()->toDateString();

        // 1. Ambil ID siswa-siswa yang dibimbing oleh guru ini
        $siswaIds = PenempatanPkl::where('guru_id', $guru->id)->pluck('siswa_id');

        // 2. Data Penempatan & Absensi Hari Ini untuk Tabel
        $siswaBimbingan = PenempatanPkl::with(['siswa', 'lokasi', 'siswa.absensi' => function($q) use ($hariIni) {
            $q->whereDate('tanggal', $hariIni);
        }])
        ->where('guru_id', $guru->id)
        ->get()
        ->map(function($penempatan) {
            $penempatan->absen_hari_ini = $penempatan->siswa->absensi->first();
            return $penempatan;
        });

        // 3. Statistik Ringkasan
        $totalSiswa = $siswaIds->count();
        $hadirHariIni = Absensi::whereIn('user_id', $siswaIds)
            ->whereDate('tanggal', $hariIni)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $statistik = [
            'total_siswa'    => $totalSiswa,
            'hadir_hari_ini' => $hadirHariIni,
            'belum_absen'    => $totalSiswa - $hadirHariIni,
        ];

        // 4. Ambil Izin yang Pending (Hanya dari siswa bimbingan)
        $izinPending = Izin::whereIn('user_id', $siswaIds)
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
        
        $totalIzinPending = $izinPending->count();

        // 5. Data Chart 7 Hari Terakhir
        $chartData = [
            'labels' => [],
            'hadir' => [],
            'terlambat' => [],
            'tidakHadir' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            
            $chartData['labels'][] = $date->translatedFormat('d M');
            
            // Hitung hadir & terlambat
            $chartData['hadir'][] = Absensi::whereIn('user_id', $siswaIds)
                ->whereDate('tanggal', $dateString)->where('status', 'hadir')->count();
                
            $chartData['terlambat'][] = Absensi::whereIn('user_id', $siswaIds)
                ->whereDate('tanggal', $dateString)->where('status', 'terlambat')->count();
                
            // Hitung yang tidak absen (Alpha/Tidak Hadir)
            $absenCount = Absensi::whereIn('user_id', $siswaIds)->whereDate('tanggal', $dateString)->count();
            $chartData['tidakHadir'][] = max(0, $totalSiswa - $absenCount);
        }

        return view('guru.dashboard', compact(
            'guru', 
            'statistik', 
            'siswaBimbingan', 
            'izinPending', 
            'totalIzinPending', 
            'chartData'
        ));
    }
}