@extends('layouts.admin')

@section('title', 'Detail siswa')
@section('subtitle', $siswa->nama)

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center overflow-hidden">
                    @if($siswa->foto_profil)
                    <img src="{{ $siswa->foto_profil_url }}" class="w-full h-full object-cover">
                    @else
                    <span class="text-primary-600 text-3xl font-bold">{{ $siswa->inisial }}</span>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-800 mt-4">{{ $siswa->nama }}</h3>
                <p class="text-gray-500">{{ $siswa->nisn }}</p>

                @if($siswa->status == 'aktif')
                <span class="inline-flex mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Aktif</span>
                @else
                <span class="inline-flex mt-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Nonaktif</span>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Email</span>
                    <span class="text-gray-800">{{ $siswa->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Telepon</span>
                    <span class="text-gray-800">{{ $siswa->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jurusan</span>
                    <span class="text-gray-800">{{ $siswa->jurusan ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tempat PKL</span>
                    <span class="text-gray-800">
                        {{ $siswa->lokasi->nama_tempat_pkl ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jenis Kelamin</span>
                    <span class="text-gray-800">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="block w-full text-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Edit Data
                </a>
            </div>
        </div>

        <!-- Absensi Bulan Ini -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">📋 Absensi Bulan Ini</h3>
            </div>
            <div class="p-6">
                @if($absensi->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Masuk</th>
                                <th class="pb-3">Pulang</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($absensi as $a)
                            <tr>
                                <td class="py-3 text-sm text-gray-800">{{ $a->tanggal->format('d M Y') }}</td>
                                <td class="py-3 text-sm text-gray-600">{{ $a->jam_masuk ?? '-' }}</td>
                                <td class="py-3 text-sm text-gray-600">{{ $a->jam_pulang ?? '-' }}</td>
                                <td class="py-3">
                                    @php
                                    $colors = [
                                    'hadir' => 'bg-green-100 text-green-700',
                                    'terlambat' => 'bg-yellow-100 text-yellow-700',
                                    'alpha' => 'bg-red-100 text-red-700',
                                    'izin_sakit' => 'bg-blue-100 text-blue-700',
                                    'izin_dinas' => 'bg-purple-100 text-purple-700',
                                    ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $colors[$a->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $a->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    Belum ada data absensi bulan ini
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection