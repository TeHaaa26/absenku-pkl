<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LokasiPkl; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Tambahkan with('lokasi') agar query lebih efisien
        $query = User::siswa()->with('lokasi');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siswa = $query->orderBy('nama')->paginate(15);

        return view('admin.siswa.index', compact('siswa'));
    }

    public function create()
    {
        // Ambil data lokasi untuk dropdown di halaman tambah
        $lokasi_pkl = LokasiPkl::all();
        return view('admin.siswa.create', compact('lokasi_pkl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:30|unique:users',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'lokasi_id' => 'nullable|exists:lokasi_pkl,id', // Tambahkan validasi lokasi
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->except(['password', 'foto_profil']);
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'siswa';
        $data['status'] = 'aktif';

        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        User::create($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $siswa = User::siswa()->findOrFail($id);
        // 2. AMBIL DATA LOKASI UNTUK DROPDOWN
        $lokasi_pkl = LokasiPkl::all();

        return view('admin.siswa.edit', compact('siswa', 'lokasi_pkl'));
    }

    public function update(Request $request, $id)
    {
        $siswa = User::siswa()->findOrFail($id);

        $request->validate([
            'nisn' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($siswa->id)],
            'nama' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($siswa->id)],
            'lokasi_id' => 'nullable|exists:lokasi_pkl,id', // Tambahkan validasi lokasi
            'password' => 'nullable|string|min:8',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->except(['password', 'foto_profil']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($siswa->foto_profil) {
                Storage::disk('public')->delete($siswa->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function show($id)
    {
        $siswa = User::siswa()->findOrFail($id);

        $absensi = $siswa->absensi()
            ->bulanIni()
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.siswa.show', compact('siswa', 'absensi'));
    }
    public function destroy($id)
    {
        $siswa = User::siswa()->findOrFail($id);

        // Hapus foto profil
        if ($siswa->foto_profil) {
            Storage::disk('public')->delete($siswa->foto_profil);
        }

        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
