@extends('layouts.admin')

@section('title', 'Detail Guru')
@section('subtitle', 'Informasi profil dan daftar bimbingan guru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-primary-600">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Guru
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium">
                Edit Profil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-4 border-4 border-primary-50">
                    @if($guru->foto_profil)
                        <img src="{{ asset('storage/' . $guru->foto_profil) }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <span class="text-primary-600 font-bold text-2xl">{{ $guru->inisial }}</span>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ $guru->nama }}</h3>
                <p class="text-sm text-gray-500">NIP. {{ $guru->nip }}</p>
                
                <div class="mt-4">
                    @if($guru->status == 'aktif')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Akun Aktif</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Nonaktif</span>
                    @endif
                </div>

                <hr class="my-6 border-gray-100">

                <div class="space-y-4 text-left">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Email</label>
                        <p class="text-sm text-gray-800">{{ $guru->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Telepon</label>
                        <p class="text-sm text-gray-800">{{ $guru->no_telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Jabatan</label>
                        <p class="text-sm text-gray-800">{{ $guru->jabatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase">Alamat</label>
                        <p class="text-sm text-gray-800">{{ $guru->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Ringkasan Bimbingan</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600">{{ $guru->penempatan_count }}</p>
                        <p class="text-xs text-blue-600 font-medium">Siswa Dibimbing</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <p class="text-2xl font-bold text-purple-600">{{ $guru->penempatan->unique('lokasi_id')->count() }}</p>
                        <p class="text-xs text-purple-600 font-medium">Lokasi PKL Berbeda</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Daftar Siswa Binaan</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi PKL</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Periode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($guru->penempatan as $p)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($p->siswa->nama, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-800">{{ $p->siswa->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ $p->siswa->nisn }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-800">{{ $p->lokasi->nama_tempat_pkl }}</p>
                                    <p class="text-xs text-gray-500">{{ $p->lokasi->alamat }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d/m/y') }} - 
                                        {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d/m/y') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic text-sm">
                                    Belum ada siswa yang diplot ke guru ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection