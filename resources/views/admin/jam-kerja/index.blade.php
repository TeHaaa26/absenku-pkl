@extends('layouts.admin')

@section('title', 'Manajemen Jam Kerja')
@section('subtitle', 'Konfigurasi waktu operasional dan shift PKL')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in">

    <div class="lg:col-span-4">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <span id="form-icon">➕</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800" id="form-title">Tambah Shift</h3>
            </div>

           

            <form action="{{ route('admin.jam-kerja.store') }}" method="POST" id="jamKerjaForm" class="space-y-5">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Shift</label>
                    <input type="text" name="nama_shift" id="nama_shift"
                        placeholder="Contoh: Shift Pagi SMA 1" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-green-600 uppercase tracking-wider mb-2">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="jam_pulang" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-gray-400">
                    <div>
                        <label class="block text-xs font-bold text-red-500 uppercase tracking-wider mb-2">Batas Masuk</label>
                        <input type="time" name="batas_absen_masuk" id="batas_absen_masuk" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">Batas Pulang</label>
                        <input type="time" name="batas_absen_pulang" id="batas_absen_pulang" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full py-4 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 font-bold shadow-lg shadow-primary-100 transition-all transform hover:-translate-y-1 active:scale-95">
                        💾 Simpan Pengaturan
                    </button>
                    <button type="button" id="btn-reset" onclick="resetForm()"
                        class="hidden w-full py-3 text-sm font-medium text-gray-500 hover:text-red-500 transition-colors">
                        ❌ Batalkan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Aturan Jam Kerja</h3>
                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full font-medium">{{ count($jamKerjas) }} Shift Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-[0.2em]">
                            <th class="px-6 py-4 font-bold text-left">Detail Shift</th>
                            <th class="px-6 py-4 font-bold text-center">Waktu Kerja</th>
                            <th class="px-6 py-4 font-bold text-center">Toleransi Absen</th>
                            <th class="px-6 py-4 font-bold text-right text-gray-800">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($jamKerjas as $jk)
                        <tr class="group hover:bg-gray-50/80 transition-all duration-300">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-700 group-hover:text-primary-600 transition-colors capitalize">
                                        {{ $jk->nama_shift ?? 'Tanpa Nama' }}
                                    </span>
                                    <span class="text-[10px] font-mono text-gray-400 mt-1">UUID: {{ strtoupper(substr($jk->id, 0, 8)) }}...</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center px-3 py-1.5 bg-primary-50 text-primary-700 rounded-full text-sm font-bold border border-primary-100">
                                    {{ \Carbon\Carbon::parse($jk->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($jk->jam_pulang)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col text-[11px] space-y-1">
                                    <span class="text-red-500 font-medium">Masuk s/d {{ \Carbon\Carbon::parse($jk->batas_absen_masuk)->format('H:i') }}</span>
                                    <span class="text-purple-500 font-medium">Pulang s/d {{ \Carbon\Carbon::parse($jk->batas_absen_pulang)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    <button onclick="editShift({{ json_encode($jk) }})"
                                        class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.jam-kerja.destroy', $jk->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus shift ini?')"
                                            class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="text-4xl mb-3 text-gray-200">🕒</div>
                                    <p class="text-gray-400 font-medium italic">Belum ada aturan jam kerja yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function editShift(data) {
        document.getElementById('form-title').innerText = "Edit Shift Kerja";
        document.getElementById('form-icon').innerText = "✏️";
        document.getElementById('form-method').value = "PUT";

        // Sesuaikan URL action untuk update
        document.getElementById('jamKerjaForm').action = "{{ url('admin/jam-kerja') }}/" + data.id;

        document.getElementById('nama_shift').value = data.nama_shift;
        document.getElementById('jam_masuk').value = data.jam_masuk;
        document.getElementById('jam_pulang').value = data.jam_pulang;
        document.getElementById('batas_absen_masuk').value = data.batas_absen_masuk;
        document.getElementById('batas_absen_pulang').value = data.batas_absen_pulang;

        document.getElementById('btn-reset').classList.remove('hidden');
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function resetForm() {
        document.getElementById('form-title').innerText = "Tambah Shift Baru";
        document.getElementById('form-icon').innerText = "➕";
        document.getElementById('form-method').value = "POST";
        document.getElementById('jamKerjaForm').action = "{{ route('admin.jam-kerja.store') }}";
        document.getElementById('jamKerjaForm').reset();
        document.getElementById('btn-reset').classList.add('hidden');
    }
</script>
@endsection