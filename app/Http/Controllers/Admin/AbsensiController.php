<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
        
        $query = Absensi::with('user')->whereDate('tanggal', $tanggal);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $absensi = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistik
        $allAbsensi = Absensi::whereDate('tanggal', $tanggal)->get();
        $totalGuru = User::guru()->aktif()->count();
        
        $statistik = [
            'total_guru' => $totalGuru,
            'hadir' => $allAbsensi->where('status', 'hadir')->count(),
            'terlambat' => $allAbsensi->where('status', 'terlambat')->count(),
            'izin' => $allAbsensi->whereIn('status', ['izin_sakit', 'izin_dinas'])->count(),
            'belum_absen' => $totalGuru - $allAbsensi->count(),
        ];

        return view('admin.absensi.index', compact('absensi', 'tanggal', 'statistik'));
    }

    public function show($id)
    {
        $absensi = Absensi::with('user')->findOrFail($id);
        return view('admin.absensi.show', compact('absensi'));
    }
}