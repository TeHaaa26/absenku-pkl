@extends('layouts.siswa')

@section('title', 'Pengajuan Izin')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="gradient-primary px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-white text-xl font-bold">📝 Pengajuan Izin</h1>
                <p class="text-white/80 text-sm">Kelola pengajuan izin Anda</p>
            </div>
            <a href="{{ route('siswa.izin.create') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-medium">
                + Ajukan
            </a>
        </div>
    </div>
    
    <div class="px-4 py-4 pb-24">
        @forelse($izinList as $izin)
        <div class="bg-white rounded-xl card-shadow p-4 mb-3">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        @if($izin->jenis_izin == 'sakit')
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                🏥 Izin Sakit
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                ✈️ Izin Dinas
                            </span>
                        @endif
                        
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'disetujui' => 'bg-green-100 text-green-700',
                                'ditolak' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$izin->status] }}">
                            {{ $izin->status_label }}
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-1">
                        📅 {{ $izin->tanggal_mulai->format('d M Y') }} 
                        @if($izin->jumlah_hari > 1)
                            - {{ $izin->tanggal_selesai->format('d M Y') }}
                        @endif
                        <span class="text-gray-400">({{ $izin->jumlah_hari }} hari)</span>
                    </p>
                    
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $izin->keterangan }}</p>
                    
                    @if($izin->catatan_approval)
                    <div class="mt-2 p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Catatan Admin:</p>
                        <p class="text-sm text-gray-700">{{ $izin->catatan_approval }}</p>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('siswa.izin.show', $izin->id) }}" class="text-primary-600 text-sm ml-4">
                    Detail →
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl card-shadow p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 mb-4">Belum ada pengajuan izin</p>
            <a href="{{ route('siswa.izin.create') }}" class="inline-block px-6 py-2 bg-primary-600 text-white rounded-lg font-medium">
                Ajukan Izin
            </a>
        </div>
        @endforelse
        
        <!-- Pagination -->
        @if($izinList->hasPages())
        <div class="mt-4">
            {{ $izinList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection