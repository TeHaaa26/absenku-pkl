<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PenempatanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa yang dibimbing oleh guru yang sedang login.
     */
    public function index(Request $request)
    {
        $guruId = Auth::guard('guru')->id();

        // Ambil ID siswa yang terdaftar di penempatan guru tersebut
        $siswaIds = PenempatanPkl::where('guru_id', $guruId)->pluck('siswa_id');

        // Query data siswa berdasarkan ID tersebut
        $query = User::siswa()
            ->with('lokasi') // Eager load lokasi agar tidak N+1
            ->whereIn('id', $siswaIds);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswa = $query->orderBy('nama')->paginate(15);

        return view('guru.siswa.index', compact('siswa'));
    }

    /**
     * Menampilkan detail profil dan riwayat absensi siswa tertentu.
     */
    public function show($id)
    {
        $guruId = Auth::guard('guru')->id();

        // Pastikan siswa yang diakses memang benar bimbingan guru tersebut
        $isBimbingan = PenempatanPkl::where('guru_id', $guruId)
            ->where('siswa_id', $id)
            ->exists();

        if (!$isBimbingan) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $siswa = User::siswa()->with('lokasi')->findOrFail($id);

        // Ambil riwayat absensi bulan ini
        $absensi = $siswa->absensi()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.siswa.show', compact('siswa', 'absensi'));
    }
}