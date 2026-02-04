<?php

namespace App\Http\Controllers\Admin; // Pastikan 'A' kapital

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;        // Tambahkan ini
use App\Models\Guru;        // Tambahkan ini
use App\Models\LokasiPkl;   // Tambahkan ini
use App\Models\PenempatanPkl; // Model untuk tabel penghubung

class PenempatanController extends Controller
{
    public function index()
    {
        $penempatans = PenempatanPkl::with(['siswa', 'guru', 'lokasi'])->latest()->get();
        // Mengacu ke folder penempatan-pkl
        return view('admin.penempatan-pkl.index', compact('penempatans'));
    }

    public function create()
    {
        $siswas = User::where('role', 'siswa')->whereDoesntHave('penempatan')->get();
        $gurus = Guru::all();
        $lokasis = LokasiPkl::all();
        // Mengacu ke folder penempatan-pkl
        return view('admin.penempatan-pkl.create', compact('siswas', 'gurus', 'lokasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'guru_id' => 'required|exists:gurus,id',
            'lokasi_id' => 'required|exists:lokasi_pkl,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // Simpan penempatan
        \App\Models\PenempatanPkl::create($request->all());

        // Update lokasi_id di tabel users (Siswa)
        \App\Models\User::where('id', $request->siswa_id)->update([
            'lokasi_id' => $request->lokasi_id
        ]);

        return redirect()->route('admin.penempatan-pkl.index')->with('success', 'Data penempatan berhasil disimpan!');
    }
}
