<?php
// app/Http/Controllers/siswa/AbsensiController.php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\AbsensiService;
use App\Models\LokasiPkl;
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
        $user = Auth::user();

        // Ambil lokasi melalui relasi penempatan
        $lokasi = $user->lokasi;

        if (!$lokasi) {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda belum ditempatkan di lokasi PKL manapun.');
        }

        // AMBIL JAM KERJA DARI LOKASI (Inilah kuncinya)
        // Jika jam_kerja_id di tabel lokasi_pkl adalah 2, maka $lokasi->jamKerja akan mengambil ID 2
        $jamKerja = $lokasi->jamKerja;

        if (!$jamKerja) {
            return redirect()->route('siswa.dashboard')->with('error', 'Jam kerja untuk lokasi ini belum diatur oleh admin.');
        }

        $statusHariIni = $this->absensiService->getStatusHariIni($user);

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        return view('siswa.absensi.index', compact('lokasi', 'statusHariIni', 'absensi', 'jamKerja'));
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
            return redirect()->route('siswa.dashboard')
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
