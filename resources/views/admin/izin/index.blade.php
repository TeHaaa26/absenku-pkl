@extends('layouts.admin')

@section('title', 'Pengajuan Izin')
@section('subtitle', 'Kelola pengajuan izin guru')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="jenis" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Jenis</option>
                <option value="sakit" {{ request('jenis') == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                <option value="dinas" {{ request('jenis') == 'dinas' ? 'selected' : '' }}>Izin Dinas</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Filter
            </button>
            @if(request()->hasAny(['status', 'jenis']))
            <a href="{{ route('admin.izin.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50">
                Reset
            </a>
            @endif
        </form>
    </div>

    @if($totalPending > 0)
    <!-- Alert Pending -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center">
        <span class="text-2xl mr-3">⏳</span>
        <div>
            <p class="font-medium text-yellow-800">{{ $totalPending }} pengajuan izin menunggu persetujuan</p>
            <p class="text-sm text-yellow-600">Segera review pengajuan yang masuk</p>
        </div>
    </div>
    @endif

    <!-- Tabel -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($izinList as $izin)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold text-sm">{{ $izin->user->inisial }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">{{ $izin->user->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $izin->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($izin->jenis_izin == 'sakit')
                                <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    🏥 Sakit
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    ✈️ Dinas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800">{{ $izin->tanggal_mulai->format('d M Y') }}</p>
                            @if($izin->jumlah_hari > 1)
                            <p class="text-xs text-gray-500">s/d {{ $izin->tanggal_selesai->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-medium text-gray-800">{{ $izin->jumlah_hari }} hari</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'disetujui' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$izin->status] }}">
                                {{ $izin->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.izin.show', $izin->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                {{ $izin->status == 'pending' ? 'Review' : 'Detail' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data pengajuan izin
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($izinList->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $izinList->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection