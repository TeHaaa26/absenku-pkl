<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4F46E5">
    
    <title>@yield('title', 'AbsenKu') - AbsenKu</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS untuk Maps -->
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
        
        /* Hide scrollbar */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Gradient background */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Card shadow */
        .card-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        /* Bottom nav safe area */
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        
        /* Active nav item */
        .nav-active {
            color: #4f46e5;
        }
        .nav-active svg {
            stroke: #4f46e5;
        }
        
        /* Pulse animation */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }
        .pulse-ring {
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen pb-20">
    
    <!-- Flash Messages -->
    @if(session('success'))
    <div id="flash-success" class="fixed top-4 left-4 right-4 z-50 bg-green-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center animate-pulse">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-success').style.display = 'none';
        }, 3000);
    </script>
    @endif
    
    @if(session('error'))
    <div id="flash-error" class="fixed top-4 left-4 right-4 z-50 bg-red-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-error').style.display = 'none';
        }, 3000);
    </script>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-lg z-40 safe-bottom">
        <div class="flex justify-around items-center py-2 px-2 max-w-lg mx-auto">
            <!-- Beranda -->
            <a href="{{ route('guru.dashboard') }}" class="flex flex-col items-center justify-center py-2 px-3 rounded-xl transition-all {{ request()->routeIs('guru.dashboard') ? 'nav-active bg-primary-50' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Beranda</span>
            </a>
            
            <!-- Absen -->
            <a href="{{ route('guru.absensi.index') }}" class="flex flex-col items-center justify-center py-2 px-3 rounded-xl transition-all {{ request()->routeIs('guru.absensi.*') ? 'nav-active bg-primary-50' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Absen</span>
            </a>
            
            <!-- Izin -->
            <a href="{{ route('guru.izin.index') }}" class="flex flex-col items-center justify-center py-2 px-3 rounded-xl transition-all {{ request()->routeIs('guru.izin.*') ? 'nav-active bg-primary-50' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Izin</span>
            </a>
            
            <!-- Riwayat -->
            <a href="{{ route('guru.riwayat.index') }}" class="flex flex-col items-center justify-center py-2 px-3 rounded-xl transition-all {{ request()->routeIs('guru.riwayat.*') ? 'nav-active bg-primary-50' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Riwayat</span>
            </a>
            
            <!-- Profil -->
            <a href="{{ route('guru.profil.index') }}" class="flex flex-col items-center justify-center py-2 px-3 rounded-xl transition-all {{ request()->routeIs('guru.profil.*') ? 'nav-active bg-primary-50' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Profil</span>
            </a>
        </div>
    </nav>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @stack('scripts')
</body>
</html>