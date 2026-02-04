<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index()
    {
        // Jika belum punya lokasi, langsung kasih alert dan balik ke dashboard
        if (!auth()->user()->lokasi) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Anda belum bisa mengakses menu Izin karena lokasi PKL belum diatur.');
        }

        $izinList = Izin::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('siswa.izin.index', compact('izinList'));
    }

    public function create()
    {
        // Sama juga untuk halaman buat izin
        if (!auth()->user()->lokasi) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Anda belum bisa mengajukan izin karena lokasi PKL belum diatur.');
        }

        return view('siswa.izin.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // PROTEKSI DOUBLE: Pastikan saat simpan pun lokasi sudah ada
        if (!$user->lokasi) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses ditolak. Lokasi PKL belum diatur.');
        }

        $request->validate([
            'jenis_izin' => 'required|in:sakit,dinas',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10',
            'lampiran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'jenis_izin.required' => 'Jenis izin harus dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai minimal hari ini',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'lampiran.required' => 'Lampiran wajib diupload',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, atau PDF',
            'lampiran.max' => 'Ukuran lampiran maksimal 2MB',
        ]);

        // Hitung jumlah hari
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Upload lampiran
        $lampiranPath = $request->file('lampiran')->store(
            'izin/' . Auth::id() . '/' . date('Y-m'),
            'public'
        );

        // Simpan izin
        Izin::create([
            'user_id' => Auth::id(),
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari' => $jumlahHari,
            'keterangan' => $request->keterangan,
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        return redirect()->route('siswa.izin.index')
            ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show($id)
    {
        $izin = Izin::where('user_id', Auth::id())->findOrFail($id);
        return view('siswa.izin.show', compact('izin'));
    }
}
