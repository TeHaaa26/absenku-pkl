<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamKerja;
use App\Models\LokasiPkl; // Tambahkan ini agar lebih rapi
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    public function index()
    {
        // Load relasi lokasis agar bisa ditampilkan di tabel & diolah JS saat edit
        $jamKerjas = JamKerja::with('lokasis')->get(); 
        $lokasis = LokasiPkl::all(); 
        return view('admin.jam-kerja.index', compact('jamKerjas', 'lokasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_shift' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'batas_absen_masuk' => 'required', // Tadi ini belum ada di validated
            'batas_absen_pulang' => 'required', // Tadi ini belum ada di validated
            'lokasi_ids' => 'nullable|array', 
        ]);

        // Simpan data shift (hanya kolom yang ada di fillable JamKerja)
        $jk = JamKerja::create($request->except('lokasi_ids'));

        // Update lokasi_Pkl agar tersambung ke shift ini
        if ($request->has('lokasi_ids')) {
            LokasiPkl::whereIn('id', $request->lokasi_ids)
                ->update(['jam_kerja_id' => $jk->id]);
        }

        return back()->with('success', 'Shift dan lokasi berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'batas_absen_masuk' => 'required',
            'batas_absen_pulang' => 'required',
            'lokasi_ids' => 'nullable|array',
        ]);

        $jamKerja = JamKerja::findOrFail($id);
        
        // 1. Update data jam kerja itu sendiri
        $jamKerja->update($request->except('lokasi_ids'));

        // 2. RESET dulu: semua lokasi yang tadinya pakai shift ini, kita lepas dulu
        LokasiPkl::where('jam_kerja_id', $jamKerja->id)
            ->update(['jam_kerja_id' => null]);

        // 3. SET ULANG: lokasi yang dicentang di form sekarang pakai shift ini
        if ($request->has('lokasi_ids')) {
            LokasiPkl::whereIn('id', $request->lokasi_ids)
                ->update(['jam_kerja_id' => $jamKerja->id]);
        }

        return back()->with('success', 'Jam kerja dan lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Sebelum hapus, lepaskan dulu kaitan di lokasi_pkl agar tidak error foreign key
        LokasiPkl::where('jam_kerja_id', $id)->update(['jam_kerja_id' => null]);

        JamKerja::destroy($id);
        return back()->with('success', 'Shift berhasil dihapus.');
    }
}