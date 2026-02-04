@extends('layouts.guru')

@section('title', 'Detail Pengajuan Izin')
@section('subtitle', 'Review pengajuan izin siswa bimbingan')

@section('content')
<div class="max-w-3xl space-y-6">
    @php
        $statusBg = [
            'pending' => 'bg-yellow-500',
            'disetujui' => 'bg-green-500',
            'ditolak' => 'bg-red-500',
        ];
    @endphp
    <div class="{{ $statusBg[$izin->status] }} rounded-2xl p-4 text-white flex items-center justify-between shadow-lg shadow-opacity-20">
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
                <p class="text-sm opacity-90">Diproses {{ $izin->approved_at->format('d M Y H:i') }} oleh {{ $izin->approver->nama ?? 'Pembimbing' }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Data Siswa Pemohon
        </h3>
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-indigo-600 font-bold text-xl">{{ substr($izin->user->nama, 0, 1) }}</span>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-gray-800 text-lg">{{ $izin->user->nama }}</p>
                <p class="text-sm text-gray-500 font-medium">NISN: {{ $izin->user->nisn ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $izin->user->kelas ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Detail Pengajuan
        </h3>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Jenis Izin</p>
                    <p class="font-medium text-gray-800">
                        @if($izin->jenis_izin == 'sakit')
                            🏥 Izin Sakit
                        @else
                            ✈️ Izin Dinas/Lainnya
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
                        s/d {{ $izin->tanggal_selesai->format('d M Y') }}
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Keterangan Siswa</p>
                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg mt-1 border border-gray-100 italic">"{{ $izin->keterangan }}"</p>
            </div>
            
            @if($izin->lampiran)
            <div>
                <p class="text-sm text-gray-500 mb-2">Lampiran Bukti</p>
                <a href="{{ asset('storage/' . $izin->lampiran) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Lihat Dokumen / Foto
                </a>
            </div>
            @endif
            
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-medium italic">Diajukan pada {{ $izin->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($izin->catatan_approval)
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-2 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            Catatan Pembimbing
        </h3>
        <p class="text-gray-700">{{ $izin->catatan_approval }}</p>
    </div>
    @endif

    @if($izin->status == 'pending')
    <div class="bg-white rounded-2xl shadow-md border-2 border-indigo-50 p-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center text-indigo-600">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Keputusan Pembimbing
        </h3>
        <form action="{{ route('guru.izin.approve', $izin->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Berikan Alasan/Catatan (Opsional)</label>
                <textarea name="catatan" rows="3" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                          placeholder="Contoh: Silahkan istirahat, atau lampiran kurang jelas..."></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" name="status" value="disetujui" 
                        class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold flex items-center justify-center transition shadow-lg shadow-green-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Izin
                </button>
                <button type="submit" name="status" value="ditolak"
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold flex items-center justify-center transition shadow-lg shadow-red-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Izin
                </button>
            </div>
        </form>
    </div>
    @endif

    <a href="{{ route('guru.izin.index') }}" class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700 transition">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Izin
    </a>
</div>
@endsection