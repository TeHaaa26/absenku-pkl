<?php
// app/Http/Controllers/Guru/IzinController.php

namespace App\Http\Controllers\Guru;

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
        $izinList = Izin::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('guru.izin.index', compact('izinList'));
    }

    public function create()
    {
        return view('guru.izin.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('guru.izin.index')
                        ->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show($id)
    {
        $izin = Izin::where('user_id', Auth::id())->findOrFail($id);
        return view('guru.izin.show', compact('izin'));
    }
}