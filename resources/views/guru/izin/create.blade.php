@extends('layouts.guru')

@section('title', 'Ajukan Izin')

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
                <h1 class="text-white text-xl font-bold">📝 Ajukan Izin</h1>
                <p class="text-white/80 text-sm">Isi form pengajuan izin</p>
            </div>
        </div>
    </div>
    
    <div class="px-4 py-4 pb-24">
        <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <!-- Jenis Izin -->
            <div class="bg-white rounded-xl card-shadow p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Izin <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" name="jenis_izin" value="sakit" class="peer sr-only" {{ old('jenis_izin') == 'sakit' ? 'checked' : '' }} required>
                        <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                            <span class="text-2xl">🏥</span>
                            <p class="font-medium text-gray-800 mt-2">Izin Sakit</p>
                            <p class="text-xs text-gray-500">Lampirkan surat dokter</p>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="jenis_izin" value="dinas" class="peer sr-only" {{ old('jenis_izin') == 'dinas' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <span class="text-2xl">✈️</span>
                            <p class="font-medium text-gray-800 mt-2">Izin Dinas</p>
                            <p class="text-xs text-gray-500">Lampirkan surat tugas</p>
                        </div>
                    </label>
                </div>
                @error('jenis_izin')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tanggal -->
            <div class="bg-white rounded-xl card-shadow p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tanggal_mulai') border-red-500 @enderror">
                        @error('tanggal_mulai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tanggal_selesai') border-red-500 @enderror">
                        @error('tanggal_selesai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Keterangan -->
            <div class="bg-white rounded-xl card-shadow p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan" rows="4" placeholder="Jelaskan alasan pengajuan izin..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Lampiran -->
            <div class="bg-white rounded-xl card-shadow p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lampiran <span class="text-red-500">*</span>
                    <span class="font-normal text-gray-500">(Surat Dokter / Surat Tugas)</span>
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                    <input type="file" name="lampiran" id="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required>
                    <label for="lampiran" class="cursor-pointer">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-gray-600">Klik untuk upload file</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau PDF (Max 2MB)</p>
                    </label>
                    <p id="file-name" class="text-sm text-primary-600 mt-2 hidden"></p>
                </div>
                @error('lampiran')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-4 rounded-xl shadow-lg">
                📤 Kirim Pengajuan
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('lampiran').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileNameEl = document.getElementById('file-name');
        if (fileName) {
            fileNameEl.textContent = '📎 ' + fileName;
            fileNameEl.classList.remove('hidden');
        }
    });
</script>
@endpush