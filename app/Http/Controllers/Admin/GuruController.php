<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = User::guru();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $guru = $query->orderBy('nama')->paginate(15);

        return view('admin.guru.index', compact('guru'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:30|unique:users',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $data = $request->except(['password', 'foto_profil']);
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'guru';
        $data['status'] = 'aktif';

        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        User::create($data);

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $guru = User::guru()->findOrFail($id);
        
        $absensi = $guru->absensi()
                       ->bulanIni()
                       ->orderBy('tanggal', 'desc')
                       ->get();

        return view('admin.guru.show', compact('guru', 'absensi'));
    }

    public function edit($id)
    {
        $guru = User::guru()->findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = User::guru()->findOrFail($id);

        $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($guru->id)],
            'nama' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($guru->id)],
            'password' => 'nullable|string|min:8',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->except(['password', 'foto_profil']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama
            if ($guru->foto_profil) {
                Storage::disk('public')->delete($guru->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = User::guru()->findOrFail($id);
        
        // Hapus foto profil
        if ($guru->foto_profil) {
            Storage::disk('public')->delete($guru->foto_profil);
        }
        
        $guru->delete();

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data guru berhasil dihapus.');
    }
}