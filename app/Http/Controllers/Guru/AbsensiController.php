<?php
// app/Http/Controllers/Guru/AbsensiController.php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use App\Models\LokasiSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('guru.absensi.index', compact('lokasi', 'statusHariIni'));
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