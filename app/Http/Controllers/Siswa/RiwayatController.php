<?php
// app/Http/Controllers/Siswa/RiwayatController.php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function index(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $user = Auth::user();

        $riwayat = Absensi::where('user_id', $user->id)
                         ->byBulan($bulan, $tahun)
                         ->orderBy('tanggal', 'desc')
                         ->get();

        $rekap = $this->absensiService->getRekapBulanan($user, $bulan, $tahun);

        // Generate list bulan untuk filter
        $listBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $listBulan[$i] = Carbon::createFromDate(null, $i, 1)->isoFormat('MMMM');
        }

        // Generate list tahun (2 tahun ke belakang sampai tahun ini)
        $tahunSekarang = Carbon::now()->year;
        $listTahun = range($tahunSekarang - 2, $tahunSekarang);

        return view('siswa.riwayat.index', compact(
            'riwayat',
            'rekap',
            'bulan',
            'tahun',
            'listBulan',
            'listTahun'
        ));
    }

    public function detail($id)
    {
        $absensi = Absensi::where('user_id', Auth::id())
                         ->findOrFail($id);

        return view('siswa.riwayat.detail', compact('absensi'));
    }
}