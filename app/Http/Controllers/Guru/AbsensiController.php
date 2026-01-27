<?php
// app/Http/Controllers/Guru/AbsensiController.php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use App\Models\LokasiSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function index()
    {
    $lokasi = LokasiSekolah::first();
    $statusHariIni = $this->absensiService->getStatusHariIni(Auth::user());

    $absensi = Absensi::where('user_id', auth()->id())
        ->where('tanggal', now()->toDateString())
        ->first();

    return view('guru.absensi.index', compact(
        'lokasi',
        'statusHariIni',
        'absensi'
    ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
        ], [
            'tipe.required' => 'Tipe absensi harus dipilih',
            'latitude.required' => 'Lokasi tidak terdeteksi',
            'longitude.required' => 'Lokasi tidak terdeteksi',
            'foto.required' => 'Foto selfie harus diambil',
        ]);

        $data = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto' => $request->foto,
        ];

        if ($request->tipe === 'masuk') {
            $result = $this->absensiService->absenMasuk(Auth::user(), $data);
        } else {
            $result = $this->absensiService->absenPulang(Auth::user(), $data);
        }

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 400);
        }

        if ($result['success']) {
            return redirect()->route('guru.dashboard')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function simpanKegiatan(Request $request)
    {
        $request->validate([
            'kegiatan' => 'required|string|max:2000',
        ]);

        $absensi = Absensi::where('user_id', auth()->id())
            ->where('tanggal', now()->toDateString())
            ->first();

        if (!$absensi || !$absensi->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini'
            ], 400);
        }

        $absensi->kegiatan = $request->kegiatan;
        $absensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil disimpan'
        ]);
    }
    

    public function cekLokasi(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $result = $this->absensiService->validasiLokasi(
            $request->latitude,
            $request->longitude
        );

        return response()->json($result);
    }
}
