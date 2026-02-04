<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    /**
     * Tampilkan Daftar Guru
     */
    public function index(Request $request)
    {
        $query = Guru::withCount('penempatan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $guru = $query->latest()->paginate(10);
        return view('admin.guru.index', compact('guru'));
    }

    /**
     * Form Tambah Guru
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Simpan Data Guru Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:gurus,email',
            'password' => 'required|min:8',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['status'] = 'aktif'; // default status

        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('profil_guru', 'public');
        }

        Guru::create($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    /**
     * Detail Guru
     */
    public function show($id)
    {
        $guru = Guru::with(['penempatan.siswa', 'penempatan.lokasi'])
                    ->withCount('penempatan')
                    ->findOrFail($id);

        return view('admin.guru.show', compact('guru'));
    }

    /**
     * Form Edit Guru
     */
    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Update Data Guru
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nip' => 'required|unique:gurus,nip,' . $id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:gurus,email,' . $id,
            'password' => 'nullable|min:8',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->except(['password', 'foto_profil']);

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update foto jika ada file baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($guru->foto_profil) {
                Storage::disk('public')->delete($guru->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('profil_guru', 'public');
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Hapus Guru
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);

        // Hapus foto dari storage
        if ($guru->foto_profil) {
            Storage::disk('public')->delete($guru->foto_profil);
        }

        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}