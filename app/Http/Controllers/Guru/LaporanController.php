<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PenempatanPkl;
use App\Models\LokasiPkl; // Tambahkan ini
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
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
        $guruId = Auth::guard('guru')->id();
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;
        $lokasiId = $request->lokasi_id;

        // 1. Ambil semua lokasi unik yang dibimbing guru ini untuk dropdown filter
        $daftarLokasiOption = PenempatanPkl::with('lokasi')
            ->where('guru_id', $guruId)
            ->get()
            ->pluck('lokasi')
            ->unique('id')
            ->filter(); // hapus jika ada penempatan tanpa lokasi

        // 2. Query dasar siswa bimbingan
        $query = PenempatanPkl::with(['siswa', 'lokasi'])
            ->where('guru_id', $guruId);

        // 3. Filter berdasarkan lokasi jika dipilih
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $lokasiId);
        }

        $siswaBimbingan = $query->get();

        $dataRekap = [];
        foreach ($siswaBimbingan as $p) {
            $rekap = $this->absensiService->getRekapBulanan($p->siswa, $bulan, $tahun);
            $dataRekap[] = [
                'siswa' => $p->siswa,
                'lokasi' => $p->lokasi->nama_tempat_pkl ?? 'Tidak Diketahui',
                'rekap' => $rekap,
            ];
        }

        // List untuk filter dropdown
        $listBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $listBulan[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }
        $tahunSekarang = Carbon::now()->year;
        $listTahun = range($tahunSekarang - 2, $tahunSekarang);

        return view('guru.laporan.index', compact(
            'dataRekap', 'bulan', 'tahun', 'listBulan', 'listTahun', 'daftarLokasiOption', 'lokasiId'
        ));
    }

    public function exportPdf(Request $request)
    {
        $guruId = Auth::guard('guru')->id();
        $guruNama = Auth::guard('guru')->user()->nama;
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;
        $lokasiId = $request->lokasi_id;

        $query = PenempatanPkl::with(['siswa', 'lokasi'])->where('guru_id', $guruId);

        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $lokasiId);
            $lokasiTerpilih = LokasiPkl::find($lokasiId)->nama_tempat_pkl;
        } else {
            $siswaBimbinganAll = PenempatanPkl::with('lokasi')->where('guru_id', $guruId)->get();
            $lokasiTerpilih = $siswaBimbinganAll->pluck('lokasi.nama_tempat_pkl')->unique()->filter()->implode(', ');
        }

        $siswaBimbingan = $query->get();
        
        $dataRekap = [];
        foreach ($siswaBimbingan as $p) {
            $rekap = $this->absensiService->getRekapBulanan($p->siswa, $bulan, $tahun);
            $dataRekap[] = [
                'siswa' => $p->siswa,
                'rekap' => $rekap,
            ];
        }

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        $pdf = Pdf::loadView('guru.laporan.pdf', [
            'dataRekap' => $dataRekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'guruNama' => $guruNama,
            'daftarLokasi' => $lokasiTerpilih ?: 'Semua Lokasi'
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download("Laporan_Bimbingan_{$namaBulan}_{$tahun}.pdf");
    }
}