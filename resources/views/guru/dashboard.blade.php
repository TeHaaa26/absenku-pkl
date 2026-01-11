@extends('layouts.guru')

@section('title', 'Beranda')

@section('content')
<div class="min-h-screen">
    <!-- Header dengan Gradient -->
    <div class="gradient-primary px-4 pt-8 pb-24 rounded-b-3xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-white/80 text-sm">{{ $greeting }} {{ $emoji }}</p>
                <h1 class="text-white text-xl font-bold">{{ $user->nama }}</h1>
                <p class="text-white/60 text-sm">{{ $user->nip }}</p>
            </div>
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center overflow-hidden">
                @if($user->foto_profil)
                    <img src="{{ $user->foto_profil_url }}" alt="Foto" class="w-full h-full object-cover">
                @else
                    <span class="text-white text-xl font-bold">{{ $user->inisial }}</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="px-4 -mt-16 pb-6">
        <!-- Card Status Hari Ini -->
        <div class="bg-white rounded-2xl card-shadow p-5 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-gray-500 text-sm">📅 {{ $statusHariIni['tanggal_lengkap'] }}</p>
                </div>
                <span id="live-clock" class="text-lg font-bold text-primary-600"></span>
            </div>
            
            @if($statusHariIni['is_libur'])
                <!-- Status Libur -->
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <span class="text-4xl">🏖️</span>
                    <p class="text-gray-800 font-semibold mt-2">Hari Libur</p>
                    <p class="text-gray-500 text-sm">{{ $statusHariIni['keterangan_libur'] }}</p>
                </div>
            @else
                <!-- Status Absensi -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Absen Masuk -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2 {{ $statusHariIni['sudah_absen_masuk'] ?? false ? 'bg-green-100' : 'bg-gray-200' }}">
                            @if($statusHariIni['sudah_absen_masuk'] ?? false)
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Masuk</p>
                        <p class="text-lg font-bold {{ $statusHariIni['sudah_absen_masuk'] ?? false ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $statusHariIni['absensi']->jam_masuk ?? '--:--' }}
                        </p>
                        @if($statusHariIni['absensi'] && $statusHariIni['absensi']->status == 'terlambat')
                            <span class="inline-block mt-1 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                                Terlambat {{ $statusHariIni['absensi']->terlambat_format }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Absen Pulang -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2 {{ $statusHariIni['sudah_absen_pulang'] ?? false ? 'bg-green-100' : 'bg-gray-200' }}">
                            @if($statusHariIni['sudah_absen_pulang'] ?? false)
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Pulang</p>
                        <p class="text-lg font-bold {{ $statusHariIni['sudah_absen_pulang'] ?? false ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $statusHariIni['absensi']->jam_pulang ?? '--:--' }}
                        </p>
                    </div>
                </div>
                
                <!-- Tombol Absen Cepat -->
                @if(!($statusHariIni['sudah_absen_pulang'] ?? false))
                <a href="{{ route('guru.absensi.index') }}" class="block mt-4">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-4 text-center text-white shadow-lg">
                        <span class="font-semibold">
                            @if(!($statusHariIni['sudah_absen_masuk'] ?? false))
                                📍 Absen Masuk Sekarang
                            @else
                                🏠 Absen Pulang Sekarang
                            @endif
                        </span>
                    </div>
                </a>
                @endif
            @endif
        </div>
        
        <!-- Menu Cepat -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            <a href="{{ route('guru.absensi.index') }}" class="bg-white rounded-xl card-shadow p-4 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Absen</p>
            </a>
            
            <a href="{{ route('guru.izin.create') }}" class="bg-white rounded-xl card-shadow p-4 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 mx-auto rounded-full bg-orange-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Ajukan Izin</p>
            </a>
            
            <a href="{{ route('guru.riwayat.index') }}" class="bg-white rounded-xl card-shadow p-4 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Riwayat</p>
            </a>
        </div>
        
        <!-- Rekap Bulan Ini -->
        <div class="bg-white rounded-2xl card-shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">📊 Rekap Bulan Ini</h2>
                <a href="{{ route('guru.riwayat.index') }}" class="text-primary-600 text-sm font-medium">Lihat Detail →</a>
            </div>
            
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="p-3 bg-green-50 rounded-xl">
                    <p class="text-2xl font-bold text-green-600">{{ $rekapBulanIni['hadir'] }}</p>
                    <p class="text-xs text-gray-500">Hadir</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-xl">
                    <p class="text-2xl font-bold text-yellow-600">{{ $rekapBulanIni['terlambat'] }}</p>
                    <p class="text-xs text-gray-500">Terlambat</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <p class="text-2xl font-bold text-blue-600">{{ $rekapBulanIni['izin_sakit'] + $rekapBulanIni['izin_dinas'] }}</p>
                    <p class="text-xs text-gray-500">Izin</p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl">
                    <p class="text-2xl font-bold text-red-600">{{ $rekapBulanIni['alpha'] }}</p>
                    <p class="text-xs text-gray-500">Alpha</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    updateClock();
    setInterval(updateClock, 1000);
</script>
@endpush