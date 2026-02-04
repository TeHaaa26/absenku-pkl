@extends('layouts.guru')

@section('title', 'Profil Siswa')
@section('subtitle', 'Informasi detail dan absensi')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
            <div class="text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-primary-100 flex items-center justify-center overflow-hidden border-4 border-primary-50">
                    @if($siswa->foto_profil)
                        <img src="{{ asset('storage/' . $siswa->foto_profil) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-primary-600 text-3xl font-bold">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</span>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-800 mt-4">{{ $siswa->nama }}</h3>
                <p class="text-gray-500 text-sm italic">NISN: {{ $siswa->nisn }}</p>
                
                <div class="mt-3">
                    @if($siswa->status == 'aktif')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif PKL</span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Nonaktif</span>
                    @endif
                </div>
            </div>

            <div class="mt-8 space-y-4 text-sm">
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                    <span class="text-gray-500 font-medium">Email</span>
                    <span class="text-gray-800">{{ $siswa->email }}</span>
                </div>
                <div class="flex justify-between items-center p-2">
                    <span class="text-gray-500 font-medium">Telepon</span>
                    <span class="text-gray-800">{{ $siswa->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                    <span class="text-gray-500 font-medium">Jurusan</span>
                    <span class="text-gray-800 font-semibold">{{ $siswa->jurusan ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-2">
                    <span class="text-gray-500 font-medium">Lokasi PKL</span>
                    <span class="text-gray-800 text-right">{{ $siswa->lokasi->nama_tempat_pkl ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('guru.siswa.index') }}" class="block w-full text-center px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                    ← Kembali ke Daftar
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Riwayat Absensi (Bulan Ini)
                </h3>
            </div>
            <div class="p-6">
                @if($absensi->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="pb-3 px-2">Tanggal</th>
                                <th class="pb-3">Masuk</th>
                                <th class="pb-3">Pulang</th>
                                <th class="pb-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($absensi as $a)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-2 text-sm text-gray-800 font-medium">
                                    {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-4 text-sm text-gray-600 font-mono">{{ $a->jam_masuk ?? '--:--' }}</td>
                                <td class="py-4 text-sm text-gray-600 font-mono">{{ $a->jam_pulang ?? '--:--' }}</td>
                                <td class="py-4 text-center">
                                    @php
                                        $statusClass = match($a->status) {
                                            'hadir' => 'bg-green-100 text-green-700',
                                            'terlambat' => 'bg-yellow-100 text-yellow-700',
                                            'alpha' => 'bg-red-100 text-red-700',
                                            'izin_sakit', 'izin_dinas' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold {{ $statusClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $a->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <img src="https://illustrations.popsy.co/gray/data-analysis.svg" class="w-32 h-32 mx-auto opacity-50 mb-4" alt="No Data">
                    <p class="text-gray-500 font-medium">Belum ada aktivitas absensi tercatat bulan ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection