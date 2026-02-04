@extends('layouts.siswa')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-primary px-4 py-6">
        <h1 class="text-white text-xl font-bold">📊 Riwayat Absensi</h1>
        <p class="text-white/80 text-sm">Lihat rekap kehadiran Anda</p>
    </div>
    
    <div class="px-4 py-4 pb-24">
        <!-- Filter -->
        <div class="bg-white rounded-xl card-shadow p-4 mb-4">
            <form method="GET" action="{{ route('siswa.riwayat.index') }}" class="flex gap-2">
                <select name="bulan" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach($listBulan as $key => $value)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach($listTahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium">
                    Filter
                </button>
            </form>
        </div>
        
        <!-- Rekap -->
        <div class="bg-white rounded-xl card-shadow p-4 mb-4">
            <h3 class="font-semibold text-gray-800 mb-3">Rekap {{ $listBulan[$bulan] }} {{ $tahun }}</h3>
            <div class="grid grid-cols-5 gap-2 text-center">
                <div class="p-2 bg-green-50 rounded-lg">
                    <p class="text-lg font-bold text-green-600">{{ $rekap['hadir'] }}</p>
                    <p class="text-xs text-gray-500">Hadir</p>
                </div>
                <div class="p-2 bg-yellow-50 rounded-lg">
                    <p class="text-lg font-bold text-yellow-600">{{ $rekap['terlambat'] }}</p>
                    <p class="text-xs text-gray-500">Terlambat</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <p class="text-lg font-bold text-blue-600">{{ $rekap['izin_sakit'] }}</p>
                    <p class="text-xs text-gray-500">Sakit</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg">
                    <p class="text-lg font-bold text-purple-600">{{ $rekap['izin_dinas'] }}</p>
                    <p class="text-xs text-gray-500">Dinas</p>
                </div>
                <div class="p-2 bg-red-50 rounded-lg">
                    <p class="text-lg font-bold text-red-600">{{ $rekap['alpha'] }}</p>
                    <p class="text-xs text-gray-500">Alpha</p>
                </div>
            </div>
            
            @if($rekap['total_terlambat_menit'] > 0)
            <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-700">
                    ⏱️ Total keterlambatan: <strong>{{ floor($rekap['total_terlambat_menit'] / 60) }} jam {{ $rekap['total_terlambat_menit'] % 60 }} menit</strong>
                </p>
            </div>
            @endif
        </div>
        
        <!-- List Riwayat -->
        <div class="bg-white rounded-xl card-shadow overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Detail Harian</h3>
            </div>
            
            @forelse($riwayat as $item)
            <div class="p-4 border-b border-gray-50 last:border-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $item->tanggal->translatedFormat('l, d M Y') }}</p>
                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                            <span>Masuk: <strong class="text-gray-700">{{ $item->jam_masuk ?? '-' }}</strong></span>
                            <span>Pulang: <strong class="text-gray-700">{{ $item->jam_pulang ?? '-' }}</strong></span>
                        </div>
                        @if($item->terlambat_menit > 0)
                        <p class="text-xs text-yellow-600 mt-1">⏱️ Terlambat {{ $item->terlambat_format }}</p>
                        @endif
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'hadir' => 'bg-green-100 text-green-700',
                                'terlambat' => 'bg-yellow-100 text-yellow-700',
                                'alpha' => 'bg-red-100 text-red-700',
                                'izin_sakit' => 'bg-blue-100 text-blue-700',
                                'izin_dinas' => 'bg-purple-100 text-purple-700',
                                'libur' => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $item->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Belum ada data absensi</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection