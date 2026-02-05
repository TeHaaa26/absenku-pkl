<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Guru AbsenKu</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 3px solid #fff;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Fix Z-Index agar sidebar mobile selalu di atas peta Leaflet */
        #mainSidebar {
            z-index: 50 !important;
        }
        #sidebarOverlay {
            z-index: 40 !important;
        }
        .leaflet-container {
            z-index: 1 !important;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">
    <div id="sidebarOverlay" 
         class="fixed inset-0 bg-black/50 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden" 
         onclick="toggleSidebar()"></div>

    <div class="flex h-screen overflow-hidden">

        <aside id="mainSidebar" 
               class="fixed inset-y-0 left-0 w-64 gradient-primary transition-transform duration-300 transform -translate-x-full md:relative md:translate-x-0 md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 h-full">
                <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
                    <h1 class="text-xl font-bold text-white">📋 AbsenKu</h1>
                    <button class="text-white md:hidden" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('guru.dashboard') }}"
                        class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('guru.izin.index') }}"
                        class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('guru.izin.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pengajuan Izin

                        @php
                            $idGuru = Auth::guard('guru')->id();
                            $daftarSiswaId = \App\Models\PenempatanPkl::where('guru_id', $idGuru)->pluck('siswa_id');
                            $pendingCount = \App\Models\Izin::whereIn('user_id', $daftarSiswaId)
                                ->where('status', 'pending')
                                ->count();
                        @endphp

                        @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('guru.siswa.index') }}"
                        class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Siswa Bimbingan
                    </a>

                    <a href="{{ route('guru.absensi.index') }}"
                        class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Monitoring Absensi
                    </a>

                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-4 text-xs font-semibold text-white/60 uppercase tracking-wider">Pengaturan</p>
                    </div>

                    <a href="{{ route('guru.laporan.index') }}"
                        class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('guru.laporan.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Laporan Bulanan
                    </a>
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-semibold">
                            {{ Auth::guard('guru')->user()?->inisial }}
                        </div>
                        <div class="ml-3 flex-1 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::guard('guru')->user()?->nama }}</p>
                            <p class="text-xs text-white/60">Pembimbing</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-white/60 hover:text-white transition-colors" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex flex-col flex-1 overflow-hidden relative">
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-4 md:px-6 py-4">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="p-2 mr-3 text-gray-600 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
                            <p class="hidden sm:block text-sm text-gray-500">@yield('subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-xs md:text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-6 relative">
                @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
            } else {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100', 'pointer-events-auto');
            }
        }
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>

</html>