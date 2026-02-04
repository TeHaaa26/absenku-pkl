@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan data absensi hari ini')

@section('content')
<div class="space-y-6">
    
    @if($isLibur)
    <!-- Banner Hari Libur -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-6 text-white">
        <div class="flex items-center">
            <span class="text-4xl mr-4">🏖️</span>
            <div>
                <h3 class="text-xl font-bold">Hari Ini Libur</h3>
                <p class="opacity-90">{{ $keteranganLibur }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Siswa -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Siswa Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalSiswa }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hadir -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $statistikHariIni['hadir'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">+ {{ $statistikHariIni['terlambat'] }} terlambat</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Izin Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $statistikHariIni['izin'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Belum Absen -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Belum Absen</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $statistikHariIni['belum_absen'] }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Statistik 7 Hari Terakhir</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartMingguan"></canvas>
            </div>
        </div>

        <!-- Izin Pending -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">📝 Izin Pending</h3>
                @if($totalIzinPending > 0)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">{{ $totalIzinPending }}</span>
                @endif
            </div>
            
            @forelse($izinPending as $izin)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold text-gray-600">
                        {{ $izin->user->inisial }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-800">{{ $izin->user->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $izin->jenis_izin_label }} • {{ $izin->jumlah_hari }} hari</p>
                    </div>
                </div>
                <a href="{{ route('admin.izin.show', $izin->id) }}" class="text-primary-600 text-sm font-medium">
                    Review
                </a>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Tidak ada izin pending</p>
            </div>
            @endforelse
            
            @if($totalIzinPending > 5)
            <a href="{{ route('admin.izin.index', ['status' => 'pending']) }}" class="block text-center text-primary-600 text-sm font-medium mt-4">
                Lihat Semua →
            </a>
            @endif
        </div>
    </div>

    <!-- Tabel Absensi Terbaru -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">📋 Absensi Hari Ini</h3>
            <a href="{{ route('admin.absensi.index') }}" class="text-primary-600 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Masuk</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Pulang</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensiTerbaru as $absen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm">
                                    {{ $absen->user->inisial }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">{{ $absen->user->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $absen->user->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm {{ $absen->jam_masuk ? 'text-gray-800' : 'text-gray-400' }}">
                                {{ $absen->jam_masuk ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm {{ $absen->jam_pulang ? 'text-gray-800' : 'text-gray-400' }}">
                                {{ $absen->jam_pulang ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'hadir' => 'bg-green-100 text-green-700',
                                    'terlambat' => 'bg-yellow-100 text-yellow-700',
                                    'izin_sakit' => 'bg-blue-100 text-blue-700',
                                    'izin_dinas' => 'bg-purple-100 text-purple-700',
                                    'alpha' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$absen->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $absen->status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data absensi hari ini
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
<script>
    // Chart Mingguan
    const ctx = document.getElementById('chartMingguan').getContext('2d');
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
                    maxBarThickness: 40
                },
                {
                    label: 'Terlambat',
                    data: {!! json_encode($chartData['terlambat']) !!},
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    maxBarThickness: 40
                },
                {
                    label: 'Tidak Hadir',
                    data: {!! json_encode($chartData['tidakHadir']) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 6,
                    maxBarThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush