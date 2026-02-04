<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
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

        $siswa = User::siswa()->aktif()->orderBy('nama')->get();

        $dataRekap = [];
        foreach ($siswa as $s) {
            $rekap = $this->absensiService->getRekapBulanan($s, $bulan, $tahun);
            $dataRekap[] = [
                'siswa' => $s,
                'rekap' => $rekap,
            ];
        }

        // List bulan
        $listBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $listBulan[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }

        // List tahun
        $tahunSekarang = Carbon::now()->year;
        $listTahun = range($tahunSekarang - 2, $tahunSekarang);

        return view('admin.laporan.index', compact(
            'dataRekap',
            'bulan',
            'tahun',
            'listBulan',
            'listTahun'
        ));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
        $filename = "Laporan_Absensi_{$namaBulan}_{$tahun}.xlsx";

        return Excel::download(
            new \App\Exports\AbsensiExport($bulan, $tahun),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $siswa = User::siswa()->aktif()->orderBy('nama')->get();

        $dataRekap = [];
        foreach ($siswa as $s) {
            $rekap = $this->absensiService->getRekapBulanan($s, $bulan, $tahun);
            $dataRekap[] = [
                'siswa' => $s,
                'rekap' => $rekap,
            ];
        }

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('dataRekap', 'bulan', 'tahun', 'namaBulan'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Laporan_Absensi_{$namaBulan}_{$tahun}.pdf");
    }
}