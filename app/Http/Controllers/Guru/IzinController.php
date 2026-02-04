<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Absensi;
use App\Models\PenempatanPkl; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        // Ambil ID siswa yang dibimbing guru ini saja
        $siswaIds = PenempatanPkl::where('guru_id', $guru->id)->pluck('siswa_id');

        $query = Izin::with('user')->whereIn('user_id', $siswaIds);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_izin', $request->jenis);
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);

        // Hitung total pending khusus siswa bimbingan
        $totalPending = Izin::whereIn('user_id', $siswaIds)->where('status', 'pending')->count();

        return view('guru.izin.index', compact('izinList', 'totalPending'));
    }

    public function approve(Request $request, $id)
    {
        $guru = Auth::guard('guru')->user();
        $siswaIds = PenempatanPkl::where('guru_id', $guru->id)->pluck('siswa_id');

        // Keamanan: Pastikan guru hanya bisa approve siswa bimbingannya
        $izin = Izin::whereIn('user_id', $siswaIds)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $izin->update([
            'status' => $request->status,
            'approved_by' => Auth::id(), // ID dari guard guru
            'approved_at' => Carbon::now(),
            'catatan_approval' => $request->catatan,
        ]);

        if ($request->status === 'disetujui') {
            $tanggalMulai = Carbon::parse($izin->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($izin->tanggal_selesai);
            $statusAbsensi = $izin->jenis_izin === 'sakit' ? 'izin_sakit' : 'izin_dinas';

            $currentDate = $tanggalMulai->copy();
            while ($currentDate <= $tanggalSelesai) {
                Absensi::updateOrCreate(
                    ['user_id' => $izin->user_id, 'tanggal' => $currentDate->format('Y-m-d')],
                    ['status' => $statusAbsensi, 'keterangan' => $izin->keterangan]
                );
                $currentDate->addDay();
            }
        }

        return redirect()->route('guru.izin.index') // Perbaiki dari admin ke guru
            ->with('success', 'Status izin berhasil diperbarui.');
    }

    public function show($id)
    {
        $guru = Auth::guard('guru')->user();
        $siswaIds = \App\Models\PenempatanPkl::where('guru_id', $guru->id)->pluck('siswa_id');

        // Keamanan: Guru hanya bisa melihat detail izin siswa bimbingannya
        $izin = Izin::with(['user', 'approver'])->whereIn('user_id', $siswaIds)->findOrFail($id);

        return view('guru.izin.show', compact('izin'));
    }
}
