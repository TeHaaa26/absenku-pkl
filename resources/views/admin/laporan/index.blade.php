@extends('layouts.admin')

@section('title', 'Laporan Absensi')
@section('subtitle', 'Rekap absensi bulanan')

@section('content')
<div class="space-y-6">
    <!-- Filter & Export -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <form method="GET" class="flex gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" class="px-4 py-2 border border-gray-200 rounded-lg">
                        @foreach($listBulan as $key => $value)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" class="px-4 py-2 border border-gray-200 rounded-lg">
                        @foreach($listTahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Tampilkan
                    </button>
                </div>
            </form>
            
            <div class="flex gap-2">
                <a href="{{ route('admin.laporan.export-excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('admin.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">📊 Rekap Absensi {{ $listBulan[$bulan] }} {{ $tahun }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nama Guru</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Hadir</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Terlambat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Sakit</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Dinas</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Alpha</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Total Terlambat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($dataRekap as $index => $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $data['guru']->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $data['guru']->nip }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                {{ $data['rekap']['hadir'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                                {{ $data['rekap']['terlambat'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                {{ $data['rekap']['izin_sakit'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                {{ $data['rekap']['izin_dinas'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                                {{ $data['rekap']['alpha'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            @if($data['rekap']['total_terlambat_menit'] > 0)
                                {{ floor($data['rekap']['total_terlambat_menit'] / 60) }}j {{ $data['rekap']['total_terlambat_menit'] % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data guru
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection