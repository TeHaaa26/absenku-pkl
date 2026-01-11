@extends('layouts.admin')

@section('title', 'Detail Pengajuan Izin')
@section('subtitle', 'Review pengajuan izin')

@section('content')
<div class="max-w-3xl space-y-6">
    <!-- Status Banner -->
    @php
        $statusBg = [
            'pending' => 'bg-yellow-500',
            'disetujui' => 'bg-green-500',
            'ditolak' => 'bg-red-500',
        ];
    @endphp
    <div class="{{ $statusBg[$izin->status] }} rounded-2xl p-4 text-white flex items-center justify-between">
        <div class="flex items-center">
            @if($izin->status == 'pending')
                <span class="text-3xl mr-3">⏳</span>
            @elseif($izin->status == 'disetujui')
                <span class="text-3xl mr-3">✅</span>
            @else
                <span class="text-3xl mr-3">❌</span>
            @endif
            <div>
                <p class="font-semibold text-lg">{{ $izin->status_label }}</p>
                @if($izin->approved_at)
                <p class="text-sm opacity-90">{{ $izin->approved_at->format('d M Y H:i') }} oleh {{ $izin->approver->nama ?? 'Admin' }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Guru -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">👤 Data Pemohon</h3>
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                <span class="text-primary-600 font-bold text-xl">{{ $izin->user->inisial }}</span>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-gray-800">{{ $izin->user->nama }}</p>
                <p class="text-sm text-gray-500">{{ $izin->user->nip }}</p>
                <p class="text-sm text-gray-500">{{ $izin->user->jabatan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Izin -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">📋 Detail Pengajuan</h3>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Jenis Izin</p>
                    <p class="font-medium text-gray-800">
                        @if($izin->jenis_izin == 'sakit')
                            🏥 Izin Sakit
                        @else
                            ✈️ Izin Dinas
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Durasi</p>
                    <p class="font-medium text-gray-800">{{ $izin->jumlah_hari }} hari</p>
                </div>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Tanggal</p>
                <p class="font-medium text-gray-800">
                    {{ $izin->tanggal_mulai->format('d M Y') }}
                    @if($izin->jumlah_hari > 1)
                        - {{ $izin->tanggal_selesai->format('d M Y') }}
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Keterangan</p>
                <p class="text-gray-800">{{ $izin->keterangan }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 mb-2">Lampiran</p>
                <a href="{{ $izin->lampiran_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Lihat Lampiran
                </a>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">Diajukan pada {{ $izin->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($izin->catatan_approval)
    <!-- Catatan Approval -->
    <div class="bg-gray-50 rounded-2xl p-6">
        <h3 class="font-semibold text-gray-800 mb-2">💬 Catatan Admin</h3>
        <p class="text-gray-700">{{ $izin->catatan_approval }}</p>
    </div>
    @endif

    @if($izin->status == 'pending')
    <!-- Form Approval -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">⚡ Tindakan</h3>
        <form action="{{ route('admin.izin.approve', $izin->id) }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" name="status" value="disetujui" 
                        class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 font-semibold flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setujui
                </button>
                <button type="submit" name="status" value="ditolak"
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tolak
                </button>
            </div>
        </form>
    </div>
    @endif

    <a href="{{ route('admin.izin.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Izin
    </a>
</div>
@endsection