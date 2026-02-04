<?php

namespace App\Http\Controllers\Guru; // Pastikan namespace benar

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use App\Models\PenempatanPkl;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $guruId = Auth::guard('guru')->id();
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        
        // 1. Ambil daftar ID siswa bimbingan guru ini
        $siswaIds = PenempatanPkl::where('guru_id', $guruId)->pluck('siswa_id');

        // 2. Query absensi hanya untuk siswa bimbingan
        $query = Absensi::with('user')
            ->whereIn('user_id', $siswaIds)
            ->whereDate('tanggal', $tanggal);

        // Filter status & search (sama seperti admin)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $absensi = $query->latest()->paginate(20);

        // 3. Statistik (Hanya untuk bimbingan guru ini)
        $allAbsensi = Absensi::whereIn('user_id', $siswaIds)->whereDate('tanggal', $tanggal)->get();
        $totalSiswaBimbingan = $siswaIds->count();
        
        $statistik = [
            'total_siswa' => $totalSiswaBimbingan,
            'hadir' => $allAbsensi->where('status', 'hadir')->count(),
            'terlambat' => $allAbsensi->where('status', 'terlambat')->count(),
            'izin' => $allAbsensi->whereIn('status', ['izin_sakit', 'izin_dinas'])->count(),
            'belum_absen' => $totalSiswaBimbingan - $allAbsensi->count(),
        ];

        return view('guru.absensi.index', compact('absensi', 'tanggal', 'statistik'));
    }

    public function show($id)
    {
        // Pastikan guru tidak bisa melihat absen siswa orang lain lewat URL
        $guruId = Auth::guard('guru')->id();
        $siswaIds = PenempatanPkl::where('guru_id', $guruId)->pluck('siswa_id');

        $absensi = Absensi::with('user')
            ->whereIn('user_id', $siswaIds)
            ->findOrFail($id);

        return view('guru.absensi.show', compact('absensi'));
    }
}