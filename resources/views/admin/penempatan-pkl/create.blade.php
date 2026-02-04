@extends('layouts.admin')

@section('title', 'Tambah Penempatan PKL')
@section('subtitle', 'Menghubungkan Siswa dengan Pembimbing dan Lokasi PKL')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Custom styling agar Tom Select senada dengan input Tailwind kamu */
    .ts-control {
        border-radius: 0.5rem !important; /* rounded-lg */
        padding: 0.75rem 1rem !important; /* py-3 px-4 */
        border: 1px solid #d1d5db !important; /* border-gray-300 */
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 2px #6366f1 !important; /* focus:ring-primary-500 */
        border-color: #6366f1 !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.penempatan-pkl.index') }}" class="text-primary-600 hover:text-primary-700 flex items-center text-sm font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Form Plotting Penempatan</h3>
        </div>

        <form action="{{ route('admin.penempatan-pkl.store') }}" method="POST" class="p-8">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Siswa</label>
                    <select id="siswa_id" name="siswa_id" placeholder="Ketik nama atau NISN siswa..." required>
                        <option value="">Cari Siswa...</option>
                        @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->nisn }})</option>
                        @endforeach
                    </select>
                    @error('siswa_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Guru Pembimbing</label>
                        <select name="guru_id" class="tom-select-pembimbing" required>
                            <option value="">Cari Guru...</option>
                            @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi PKL</label>
                        <select name="lokasi_id" class="tom-select-lokasi" required>
                            <option value="">Cari Tempat PKL...</option>
                            @foreach($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama_tempat_pkl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" required>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-8 mt-4 border-t border-gray-100">
                    <button type="reset" class="px-6 py-3 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                        Reset
                    </button>
                    <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-md shadow-indigo-200 transition-all">
                        Simpan Penempatan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    // Inisialisasi untuk Siswa
    new TomSelect("#siswa_id", {
        create: false,
        sortField: { field: "text", direction: "asc" }
    });

    // Inisialisasi untuk Guru & Lokasi (Sekalian agar konsisten)
    document.querySelectorAll('.tom-select-pembimbing, .tom-select-lokasi').forEach((el) => {
        new TomSelect(el, {
            create: false,
        });
    });
</script>
@endpush