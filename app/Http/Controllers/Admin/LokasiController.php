<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiPkl; // Pastikan ini sesuai dengan nama file Model kamu
use App\Models\JamKerja;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan LokasiPkl (sesuai Model yang di-import)
        $query = LokasiPkl::with('jamKerja');

        // Logic Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_tempat_pkl', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $lokasi = $query->latest()->get();
        $jamKerjas = JamKerja::all(); 

        return view('admin.lokasi.index', compact('lokasi', 'jamKerjas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tempat_pkl' => 'required|string|max:150',
            'alamat'       => 'nullable|string',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius'       => 'required|integer|min:10|max:1000',
            'jam_kerja_id' => 'nullable|exists:jam_kerja,id', // Biasanya tabel Laravel jam_kerjas (jamak)
        ]);

        LokasiPkl::create($validated);

        return back()->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $lokasi = LokasiPkl::findOrFail($id);

        $validated = $request->validate([
            'nama_tempat_pkl' => 'required|string|max:150',
            'alamat'       => 'nullable|string',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius'       => 'required|integer|min:10|max:1000',
            'jam_kerja_id' => 'nullable|exists:jam_kerja,id',
        ]);

        $lokasi->update($validated);

        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lokasi = LokasiPkl::findOrFail($id);
        $lokasi->delete();

        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}