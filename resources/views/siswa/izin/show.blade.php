@extends('layouts.guru')

@section('title', 'Detail Izin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-primary px-4 py-6">
        <div class="flex items-center">
            <a href="{{ route('guru.izin.index') }}" class="mr-3 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-white text-xl font-bold">📄 Detail Izin</h1>
            </div>
        </div>
    </div>
    
    <div class="px-4 py-4 pb-24">
        <div class="bg-white rounded-xl card-shadow overflow-hidden">
            <!-- Status Banner -->
            @php
                $statusBg = [
                    'pending' => 'bg-yellow-500',
                    'disetujui' => 'bg-green-500',
                    'ditolak' => 'bg-red-500',
                ];
            @endphp
            <div class="{{ $statusBg[$izin->status] }} px-4 py-3 text-white text-center">
                <span class="font-semibold">{{ $izin->status_label }}</span>
                @if($izin->approved_at)
                    <span class="text-sm opacity-80">• {{ $izin->approved_at->format('d M Y H:i') }}</span>
                @endif
            </div>
            
            <div class="p-4 space-y-4">
                <!-- Jenis Izin -->
                <div>
                    <p class="text-sm text-gray-500">Jenis Izin</p>
                    <p class="font-semibold text-gray-800">
                        @if($izin->jenis_izin == 'sakit')
                            🏥 Izin Sakit
                        @else
                            ✈️ Izin Dinas
                        @endif
                    </p>
                </div>
                
                <!-- Tanggal -->
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="font-semibold text-gray-800">
                        {{ $izin->tanggal_mulai->format('d M Y') }}
                        @if($izin->jumlah_hari > 1)
                            - {{ $izin->tanggal_selesai->format('d M Y') }}
                        @endif
                        <span class="text-gray-500 font-normal">({{ $izin->jumlah_hari }} hari)</span>
                    </p>
                </div>
                
                <!-- Keterangan -->
                <div>
                    <p class="text-sm text-gray-500">Keterangan</p>
                    <p class="text-gray-800">{{ $izin->keterangan }}</p>
                </div>
                
                <!-- Lampiran -->
                <div>
                    <p class="text-sm text-gray-500 mb-2">Lampiran</p>
                    <a href="{{ $izin->lampiran_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Lihat Lampiran
                    </a>
                </div>
                
                <!-- Catatan Approval -->
                @if($izin->catatan_approval)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-500">Catatan dari Admin</p>
                    <p class="text-gray-800 mt-1">{{ $izin->catatan_approval }}</p>
                    @if($izin->approver)
                    <p class="text-xs text-gray-400 mt-2">— {{ $izin->approver->nama }}</p>
                    @endif
                </div>
                @endif
                
                <!-- Tanggal Pengajuan -->
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Diajukan pada {{ $izin->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection