@extends('layouts.admin')

@section('title', 'Detail Absensi')
@section('subtitle', $absensi->user->nama . ' - ' . $absensi->tanggal->format('d M Y'))

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Info Guru -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                <span class="text-primary-600 font-bold text-xl">{{ $absensi->user->inisial }}</span>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">{{ $absensi->user->nama }}</h3>
                <p class="text-gray-500">{{ $absensi->user->nip }}</p>
            </div>
            <div class="ml-auto">
                @php
                    $colors = [
                        'hadir' => 'bg-green-100 text-green-700',
                        'terlambat' => 'bg-yellow-100 text-yellow-700',
                        'alpha' => 'bg-red-100 text-red-700',
                        'izin_sakit' => 'bg-blue-100 text-blue-700',
                        'izin_dinas' => 'bg-purple-100 text-purple-700',
                    ];
                @endphp
                <span class="inline-flex px-4 py-2 rounded-full text-sm font-semibold {{ $colors[$absensi->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $absensi->status_label }}
                </span>
            </div>
        </div>
    </div>
    
    <!-- Detail Absensi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Absen Masuk -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-semibold text-gray-800 mb-4">📥 Absen Masuk</h4>
            @if($absensi->jam_masuk)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam</span>
                    <span class="font-semibold text-gray-800">{{ $absensi->jam_masuk }}</span>
                </div>
                @if($absensi->terlambat_menit > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">Keterlambatan</span>
                    <span class="font-semibold text-yellow-600">{{ $absensi->terlambat_format }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Jarak</span>
                    <span class="text-gray-800">{{ $absensi->jarak_masuk }} meter</span>
                </div>
                @if($absensi->foto_masuk)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Foto Selfie</p>
                    <img src="{{ $absensi->foto_masuk_url }}" alt="Foto Masuk" class="w-full rounded-xl">
                </div>
                @endif
            </div>
            @else
            <p class="text-gray-400 text-center py-8">Belum absen masuk</p>
            @endif
        </div>
        
        <!-- Absen Pulang -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-semibold text-gray-800 mb-4">📤 Absen Pulang</h4>
            @if($absensi->jam_pulang)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam</span>
                    <span class="font-semibold text-gray-800">{{ $absensi->jam_pulang }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jarak</span>
                    <span class="text-gray-800">{{ $absensi->jarak_pulang }} meter</span>
                </div>
                @if($absensi->foto_pulang)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Foto Selfie</p>
                    <img src="{{ $absensi->foto_pulang_url }}" alt="Foto Pulang" class="w-full rounded-xl">
                </div>
                @endif
            </div>
            @else
            <p class="text-gray-400 text-center py-8">Belum absen pulang</p>
            @endif
        </div>

    </div>

     <!-- Laporan kegiatan -->
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-semibold text-gray-800 mb-3">📝 Kegiatan</h4>

        @if($absensi->kegiatan)
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $absensi->kegiatan }}
            </p>
        @else
            <p class="text-gray-400">
                Belum membuat laporan
            </p>
        @endif
    </div>

    
    <a href="{{ route('admin.absensi.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Data Absensi
    </a>
</div>
@endsection