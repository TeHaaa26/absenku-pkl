<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiSekolah;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = LokasiSekolah::first();
        return view('admin.lokasi.index', compact('lokasi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
        ], [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi',
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.between' => 'Latitude tidak valid',
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.between' => 'Longitude tidak valid',
            'radius.required' => 'Radius wajib diisi',
            'radius.min' => 'Radius minimal 10 meter',
            'radius.max' => 'Radius maksimal 1000 meter',
        ]);

        LokasiSekolah::updateOrCreate(
            ['id' => 1],
            $request->only(['nama_sekolah', 'alamat', 'latitude', 'longitude', 'radius'])
        );

        return back()->with('success', 'Lokasi sekolah berhasil diperbarui.');
    }
}