@extends('layouts.admin')

@section('title', 'Hari Libur')
@section('subtitle', 'Kelola hari libur nasional dan khusus')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">➕ Tambah Hari Libur</h3>
            <form action="{{ route('admin.hari-libur.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('keterangan') border-red-500 @enderror"
                           placeholder="Contoh: Hari Raya Idul Fitri">
                    @error('keterangan')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-semibold">
                    Tambah Hari Libur
                </button>
            </form>
            
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-sm text-gray-500">
                    <strong>Catatan:</strong> Hari Minggu otomatis dianggap libur dan tidak perlu ditambahkan.
                </p>
            </div>
        </div>
        
        <!-- Daftar Hari Libur -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">📅 Daftar Hari Libur</h3>
                <form method="GET" class="flex gap-2">
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        @foreach($listTahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <div class="p-6">
                @if($hariLibur->count() > 0)
                <div class="space-y-3">
                    @foreach($hariLibur as $libur)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                                <span class="text-red-600 font-bold">{{ $libur->tanggal->format('d') }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $libur->keterangan }}</p>
                                <p class="text-sm text-gray-500">{{ $libur->tanggal->translatedFormat('l, d F Y') }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.hari-libur.destroy', $libur->id) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p>Belum ada hari libur untuk tahun {{ $tahun }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection