<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('user');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_izin', $request->jenis);
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalPending = Izin::pending()->count();

        return view('admin.izin.index', compact('izinList', 'totalPending'));
    }

    public function show($id)
    {
        $izin = Izin::with(['user', 'approver'])->findOrFail($id);
        return view('admin.izin.show', compact('izin'));
    }

    public function approve(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $izin->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'catatan_approval' => $request->catatan,
        ]);

        // Jika disetujui, update status absensi pada tanggal tersebut
        if ($request->status === 'disetujui') {
            $tanggalMulai = Carbon::parse($izin->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($izin->tanggal_selesai);
            
            $statusAbsensi = $izin->jenis_izin === 'sakit' ? 'izin_sakit' : 'izin_dinas';

            // Loop dari tanggal mulai sampai tanggal selesai
            $currentDate = $tanggalMulai->copy();
            while ($currentDate <= $tanggalSelesai) {
                Absensi::updateOrCreate(
                    [
                        'user_id' => $izin->user_id,
                        'tanggal' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'status' => $statusAbsensi,
                        'keterangan' => $izin->keterangan,
                    ]
                );
                $currentDate->addDay();
            }
        }

        $message = $request->status === 'disetujui' 
            ? 'Izin berhasil disetujui.' 
            : 'Izin berhasil ditolak.';

        return redirect()->route('admin.izin.index')
                        ->with('success', $message);
    }
}