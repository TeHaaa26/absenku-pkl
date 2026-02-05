<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Admin AbsenKu</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Leaflet CSS -->
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

        /* Pastikan Sidebar lebih tinggi dari komponen Leaflet */
        #mainSidebar {
            z-index: 50 !important;
        }

        /* Overlay juga harus di atas peta tapi di bawah sidebar */
        #sidebarOverlay {
            z-index: 40 !important;
        }

        /* Opsional: Jika Leaflet masih bandel, paksa z-index peta lebih rendah */
        .leaflet-container {
            z-index: 1 !important;
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
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">
    <div id="sidebarOverlay" class="fixed inset-0 z-20 bg-black/50 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden" onclick="toggleSidebar()"></div>

    <div class="flex h-screen overflow-hidden">

        <aside id="mainSidebar" class="fixed inset-y-0 left-0 z-30 w-64 gradient-primary transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-shrink-0">
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
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.guru.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Data Guru
                    </a>

                    <a href="{{ route('admin.siswa.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        Data Siswa
                    </a>

                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-4 text-xs font-semibold text-white/60 uppercase tracking-wider">Pengaturan</p>
                    </div>

                    <a href="{{ route('admin.lokasi.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lokasi PKL
                    </a>

                    <a href="{{ route('admin.jam-kerja.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.jam-kerja.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Jam Kerja
                    </a>

                    <a href="{{ route('admin.penempatan-pkl.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.penempatan-pkl.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                        </svg>
                        Penempatan PKL
                    </a>

                    <a href="{{ route('admin.hari-libur.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.hari-libur.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Hari Libur
                    </a>

                    <a href="{{ route('admin.laporan.index') }}" class="sidebar-link flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </a>
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-semibold">
                            {{ auth()->user()->inisial }}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->nama }}</p>
                            <p class="text-xs text-white/60">Administrator</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-white/60 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex flex-col flex-1 overflow-hidden min-w-0">
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-4 md:px-6 py-4">
                    <div class="flex items-center">
                        <button class="p-2 mr-3 text-gray-600 rounded-lg md:hidden hover:bg-gray-100" onclick="toggleSidebar()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-gray-800 truncate">@yield('title', 'Dashboard')</h2>
                            <p class="hidden sm:block text-sm text-gray-500">@yield('subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="hidden sm:block text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
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

            // Toggle overlay opacity & pointer events
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