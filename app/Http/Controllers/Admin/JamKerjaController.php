<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamKerja;
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    public function index()
    {
        $jamKerja = JamKerja::first();
        return view('admin.jam-kerja.index', compact('jamKerja'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
            'batas_absen_masuk' => 'required|date_format:H:i|after:jam_masuk',
            'batas_absen_pulang' => 'required|date_format:H:i|after:jam_pulang',
        ], [
            'jam_masuk.required' => 'Jam masuk wajib diisi',
            'jam_pulang.required' => 'Jam pulang wajib diisi',
            'jam_pulang.after' => 'Jam pulang harus setelah jam masuk',
            'batas_absen_masuk.required' => 'Batas absen masuk wajib diisi',
            'batas_absen_masuk.after' => 'Batas absen masuk harus setelah jam masuk',
            'batas_absen_pulang.required' => 'Batas absen pulang wajib diisi',
            'batas_absen_pulang.after' => 'Batas absen pulang harus setelah jam pulang',
        ]);

        JamKerja::updateOrCreate(
            ['id' => 1],
            [
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'batas_absen_masuk' => $request->batas_absen_masuk,
                'batas_absen_pulang' => $request->batas_absen_pulang,
            ]
        );

        return back()->with('success', 'Jam kerja berhasil diperbarui.');
    }
}