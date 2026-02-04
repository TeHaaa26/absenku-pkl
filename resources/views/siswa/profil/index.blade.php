@extends('layouts.siswa')

@section('title', 'Profil')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-primary px-4 pt-8 pb-20">
        <div class="text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-white/20 flex items-center justify-center overflow-hidden border-4 border-white/30">
                @if($user->foto_profil)
                    <img src="{{ $user->foto_profil_url }}" alt="Foto" class="w-full h-full object-cover">
                @else
                    <span class="text-white text-3xl font-bold">{{ $user->inisial }}</span>
                @endif
            </div>
            <h1 class="text-white text-xl font-bold mt-4">{{ $user->nama }}</h1>
            <p class="text-white/80">{{ $user->nip }}</p>
        </div>
    </div>
    
    <div class="px-4 -mt-10 pb-24">
        <!-- Info Card -->
        <div class="bg-white rounded-xl card-shadow p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">📋 Informasi Pribadi</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Email</span>
                    <span class="text-gray-800">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">No. Telepon</span>
                    <span class="text-gray-800">{{ $user->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Jenis Kelamin</span>
                    <span class="text-gray-800">{{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : ($user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Jabatan</span>
                    <span class="text-gray-800">{{ $user->jabatan ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Edit Profile Form -->
        <div class="bg-white rounded-xl card-shadow p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">✏️ Edit Profil</h3>
            <form action="{{ route('siswa.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" 
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    <input type="file" name="foto_profil" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-400 mt-1">Max 1MB (JPG, PNG)</p>
                </div>
                
                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-semibold">
                    Simpan Perubahan
                </button>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="bg-white rounded-xl card-shadow p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">🔒 Ubah Password</h3>
            <form action="{{ route('siswa.profil.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold">
                    Ubah Password
                </button>
            </form>
        </div>
        
        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-50 text-red-600 py-3 rounded-xl font-semibold border border-red-200">
                🚪 Keluar
            </button>
        </form>
    </div>
</div>
@endsection