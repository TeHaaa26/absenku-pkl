@extends('layouts.guru')

@section('title', 'Dashboard Pembimbing')
@section('subtitle', 'Monitoring siswa bimbingan PKL Anda hari ini')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ $guru->nama }}! 👋</h1>
        <p class="text-gray-500">Anda sedang membimbing <strong>{{ $statistik['total_siswa'] }} siswa</strong> di berbagai lokasi PKL.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Bimbingan</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $statistik['total_siswa'] }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $statistik['hadir_hari_ini'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Belum Absen</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $statistik['belum_absen'] }}</p>
                </div>
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ $guru->nama }}! 👋</h1>
            <p class="text-gray-500">Anda sedang membimbing <strong>{{ $statistik['total_siswa'] }} siswa</strong>.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Statistik Bimbingan (7 Hari Terakhir)</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartMingguanGuru"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">📝 Izin Menunggu</h3>
                    @if($totalIzinPending > 0)
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">{{ $totalIzinPending }}</span>
                    @endif
                </div>

                <div class="space-y-4">
                    @forelse($izinPending as $izin)
                    <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-600">
                                {{ substr($izin->user->nama, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">{{ $izin->user->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $izin->jenis_izin }} • {{ $izin->durasi }} hari</p>
                            </div>
                        </div>
                        <a href="{{ route('guru.izin.index') }}" class="text-indigo-600 text-sm font-medium hover:underline">
                            Review
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">Tidak ada permohonan izin</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">📋 Status Absensi Siswa Bimbingan (Hari Ini)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi PKL</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswaBimbingan as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($item->siswa->nama, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">{{ $item->siswa->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->siswa->nisn }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $item->lokasi->nama_tempat_pkl }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-medium {{ $item->absen_hari_ini ? 'text-gray-800' : 'text-gray-400 italic' }}">
                                {{ $item->absen_hari_ini->jam_masuk ?? 'Belum absen' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $status = $item->absen_hari_ini->status ?? 'belum_absen';
                            $statusColors = [
                            'hadir' => 'bg-green-100 text-green-700',
                            'terlambat' => 'bg-yellow-100 text-yellow-700',
                            'izin_sakit' => 'bg-blue-100 text-blue-700',
                            'alpha' => 'bg-red-100 text-red-700',
                            'belum_absen' => 'bg-gray-100 text-gray-400',
                            ];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
                                {{ str_replace('_', ' ', ucwords($status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <p class="text-gray-500 italic">Belum ada siswa yang diplot ke bimbingan Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartMingguanGuru').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Hadir',
                    data: {!! json_encode($chartData['hadir']) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                },
                {
                    label: 'Terlambat',
                    data: {!! json_encode($chartData['terlambat']) !!},
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                },
                {
                    label: 'Alpha',
                    data: {!! json_encode($chartData['tidakHadir']) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush