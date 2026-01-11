<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HariLiburController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? Carbon::now()->year;
        
        $hariLibur = HariLibur::whereYear('tanggal', $tahun)
                              ->orderBy('tanggal')
                              ->get();

        $listTahun = range(Carbon::now()->year - 1, Carbon::now()->year + 1);

        return view('admin.hari-libur.index', compact('hariLibur', 'tahun', 'listTahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:150',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.unique' => 'Tanggal sudah terdaftar sebagai hari libur',
            'keterangan.required' => 'Keterangan wajib diisi',
        ]);

        HariLibur::create($request->only(['tanggal', 'keterangan']));

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->delete();

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}