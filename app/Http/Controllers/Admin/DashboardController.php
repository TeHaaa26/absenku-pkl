<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use App\Services\AbsensiService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function index()
    {
        $today = Carbon::today();
        $totalSiswa = User::siswa()->aktif()->count();

        // Statistik hari ini
        $absensiHariIni = Absensi::with('user')
                                 ->whereDate('tanggal', $today)
                                 ->get();

        $sudahAbsen = $absensiHariIni->count();
        
        $statistikHariIni = [
            'hadir' => $absensiHariIni->where('status', 'hadir')->count(),
            'terlambat' => $absensiHariIni->where('status', 'terlambat')->count(),
            'izin' => $absensiHariIni->whereIn('status', ['izin_sakit', 'izin_dinas'])->count(),
            'belum_absen' => $totalSiswa - $sudahAbsen,
        ];

        // Izin pending
        $izinPending = Izin::with('user')
                          ->pending()
                          ->latest()
                          ->take(5)
                          ->get();

        $totalIzinPending = Izin::pending()->count();

        // Data untuk grafik mingguan
        $chartData = $this->getChartDataMingguan($totalSiswa);

        // Absensi terbaru hari ini
        $absensiTerbaru = Absensi::with('user')
                                 ->whereDate('tanggal', $today)
                                 ->latest()
                                 ->take(10)
                                 ->get();

        // Cek hari libur
        $isLibur = $this->absensiService->isHariLibur($today);
        $keteranganLibur = $this->absensiService->getKeteranganLibur($today);

        return view('admin.dashboard', compact(
            'totalSiswa',
            'statistikHariIni',
            'izinPending',
            'totalIzinPending',
            'chartData',
            'absensiTerbaru',
            'isLibur',
            'keteranganLibur'
        ));
    }

    private function getChartDataMingguan($totalSiswa)
    {
        $labels = [];
        $hadir = [];
        $terlambat = [];
        $tidakHadir = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('D');

            $absensi = Absensi::whereDate('tanggal', $date)->get();
            $hadir[] = $absensi->where('status', 'hadir')->count();
            $terlambat[] = $absensi->where('status', 'terlambat')->count();
            $tidakHadir[] = $totalSiswa - $absensi->count();
        }

        return [
            'labels' => $labels,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'tidakHadir' => $tidakHadir,
        ];
    }
}