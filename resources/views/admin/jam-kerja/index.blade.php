@extends('layouts.admin')

@section('title', 'Pengaturan Jam Kerja')
@section('subtitle', 'Atur jam masuk, pulang, dan batas absensi')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.jam-kerja.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jam Masuk -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-green-600">🟢</span> Jam Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $jamKerja->jam_masuk ?? '06:30') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('jam_masuk') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Jam guru seharusnya masuk</p>
                    @error('jam_masuk')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Jam Pulang -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-orange-600">🟠</span> Jam Pulang <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_pulang" value="{{ old('jam_pulang', $jamKerja->jam_pulang ?? '15:00') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('jam_pulang') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Jam guru seharusnya pulang</p>
                    @error('jam_pulang')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Batas Absen Masuk -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-600">🔴</span> Batas Absen Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="batas_absen_masuk" value="{{ old('batas_absen_masuk', $jamKerja->batas_absen_masuk ?? '12:00') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('batas_absen_masuk') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Batas waktu guru dapat absen masuk</p>
                    @error('batas_absen_masuk')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <!-- Batas Absen Pulang -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-purple-600">🟣</span> Batas Absen Pulang <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="batas_absen_pulang" value="{{ old('batas_absen_pulang', $jamKerja->batas_absen_pulang ?? '22:00') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('batas_absen_pulang') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Batas waktu guru dapat absen pulang</p>
                    @error('batas_absen_pulang')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h4 class="font-medium text-blue-800 mb-2">ℹ️ Informasi</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Guru yang absen <strong>setelah jam masuk</strong> akan tercatat <strong>Terlambat</strong></li>
                    <li>• Guru <strong>tidak dapat absen masuk</strong> setelah batas absen masuk</li>
                    <li>• Guru <strong>tidak dapat absen pulang</strong> setelah batas absen pulang</li>
                </ul>
            </div>
            
            <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-semibold">
                💾 Simpan Pengaturan
            </button>
        </form>
    </div>
    
    @if($jamKerja)
    <!-- Preview Jam Kerja Saat Ini -->
    <div class="mt-6 bg-gray-50 rounded-2xl p-6">
        <h3 class="font-semibold text-gray-800 mb-4">⏰ Jam Kerja Saat Ini</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-white rounded-xl p-4">
                <p class="text-2xl font-bold text-green-600">{{ \Carbon\Carbon::parse($jamKerja->jam_masuk)->format('H:i') }}</p>
                <p class="text-sm text-gray-500">Jam Masuk</p>
            </div>
            <div class="bg-white rounded-xl p-4">
                <p class="text-2xl font-bold text-orange-600">{{ \Carbon\Carbon::parse($jamKerja->jam_pulang)->format('H:i') }}</p>
                <p class="text-sm text-gray-500">Jam Pulang</p>
            </div>
            <div class="bg-white rounded-xl p-4">
                <p class="text-2xl font-bold text-red-600">{{ \Carbon\Carbon::parse($jamKerja->batas_absen_masuk)->format('H:i') }}</p>
                <p class="text-sm text-gray-500">Batas Masuk</p>
            </div>
            <div class="bg-white rounded-xl p-4">
                <p class="text-2xl font-bold text-purple-600">{{ \Carbon\Carbon::parse($jamKerja->batas_absen_pulang)->format('H:i') }}</p>
                <p class="text-sm text-gray-500">Batas Pulang</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection