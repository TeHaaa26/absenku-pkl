@extends('layouts.siswa')



@section('title', 'Absensi')



@section('content')

<div class="min-h-screen bg-gray-50">

    <!-- Header -->

    <div class="gradient-primary px-4 py-6">

        <div class="flex items-center justify-between">

            <div>

                <h1 class="text-white text-xl font-bold">📍 Absensi</h1>

                <p class="text-white/80 text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>

            </div>

            <span id="header-clock" class="text-white text-2xl font-bold"></span>

        </div>

    </div>



    <div class="px-4 py-4 pb-24">

        @if($statusHariIni['is_libur'])

        <!-- Hari Libur -->

        <div class="bg-white rounded-2xl card-shadow p-8 text-center">

            <span class="text-6xl">🏖️</span>

            <h2 class="text-xl font-bold text-gray-800 mt-4">Hari Libur</h2>

            <p class="text-gray-500 mt-2">{{ $statusHariIni['keterangan_libur'] }}</p>

            <p class="text-gray-400 text-sm mt-4">Tidak perlu melakukan absensi hari ini.</p>

        </div>

        @elseif(!$lokasi)

        <!-- Lokasi Belum Diatur -->

        <div class="bg-white rounded-2xl card-shadow p-8 text-center">

            <span class="text-6xl">⚠️</span>

            <h2 class="text-xl font-bold text-gray-800 mt-4">Lokasi Belum Diatur</h2>

            <p class="text-gray-500 mt-2">Admin belum mengatur lokasi Pkl. Silakan hubungi admin.</p>

        </div>

        @else

        <!-- Status Lokasi -->

        <div id="location-status" class="mb-4 p-4 rounded-xl bg-gray-100 border border-gray-200">

            <div class="flex items-center">

                <div class="animate-spin mr-3">

                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24">

                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>

                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>

                    </svg>

                </div>

                <span class="text-gray-600">Mendapatkan lokasi...</span>

            </div>

        </div>



        <!-- Peta -->

        <div class="bg-white rounded-2xl card-shadow overflow-hidden mb-4">

            <div id="map" class="h-48 w-full"></div>

            <div class="p-3 border-t border-gray-100">

                <p class="text-sm text-gray-600">

                    <span class="font-medium">{{ $lokasi->nama_tempat_pkl }}</span><br>

                    <span class="text-gray-400">Radius: {{ $lokasi->radius }} meter</span>

                </p>

            </div>

        </div>



        <!-- Camera Section -->

        <div class="bg-white rounded-2xl card-shadow p-4 mb-4">

            <h3 class="font-semibold text-gray-800 mb-3">📸 Foto Selfie</h3>



            <div class="relative bg-black rounded-xl overflow-hidden aspect-[4/3]">

                <video id="camera" class="w-full h-full object-cover" autoplay playsinline></video>

                <canvas id="canvas" class="hidden"></canvas>

                <img id="preview" src="" class="w-full h-full object-cover hidden">



                <!-- Camera error message -->

                <div id="camera-error" class="hidden absolute inset-0 bg-gray-800 flex items-center justify-center text-white text-center p-4">

                    <div>

                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />

                        </svg>

                        <p class="text-sm">Kamera tidak dapat diakses</p>

                        <p class="text-xs text-gray-400 mt-1">Pastikan izin kamera sudah diaktifkan</p>

                    </div>

                </div>

            </div>



            <div class="flex gap-2 mt-3">

                <button id="btn-capture" type="button" class="flex-1 bg-primary-600 text-white py-3 rounded-xl font-semibold flex items-center justify-center">

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />

                    </svg>

                    Ambil Foto

                </button>

                <button id="btn-retake" type="button" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hidden">

                    🔄 Ulangi

                </button>

            </div>

        </div>



        <!-- Tombol Absen -->

        <div class="grid grid-cols-2 gap-3">

            <button id="btn-masuk" type="button" disabled

                class="py-4 rounded-xl font-semibold text-white bg-green-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center justify-center">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />

                </svg>

                Masuk

            </button>

            <button id="btn-pulang" type="button" disabled

                class="py-4 rounded-xl font-semibold text-white bg-orange-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors flex items-center justify-center">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />

                </svg>

                Pulang

            </button>

        </div>



        <!-- Info Status -->

        <div class="mt-4 p-4 bg-white rounded-xl card-shadow">

            <h4 class="font-medium text-gray-800 mb-2">Status Hari Ini:</h4>

            <div class="flex justify-between text-sm">

                <span class="text-gray-500">Absen Masuk:</span>

                <span class="font-medium {{ ($statusHariIni['sudah_absen_masuk'] ?? false) ? 'text-green-600' : 'text-gray-400' }}">

                    {{ $statusHariIni['absensi']->jam_masuk ?? 'Belum' }}

                </span>

            </div>

            <div class="flex justify-between text-sm mt-1">

                <span class="text-gray-500">Absen Pulang:</span>

                <span class="font-medium {{ ($statusHariIni['sudah_absen_pulang'] ?? false) ? 'text-green-600' : 'text-gray-400' }}">

                    {{ $statusHariIni['absensi']->jam_pulang ?? 'Belum' }}

                </span>

            </div>

        </div>



        <!-- Kegiatan Hari Ini -->

        <div id="kegiatan-wrapper" class="mt-4 bg-white rounded-2xl card-shadow p-4 {{ ($statusHariIni['sudah_absen_masuk'] && !$statusHariIni['sudah_absen_pulang']) ? '' : 'hidden' }}">
            <h4 class="font-semibold text-gray-800 mb-2">📝 Kegiatan Hari Ini</h4>

            {{-- VIEW MODE (Tampil jika sudah ada isi kegiatan) --}}
            <div id="kegiatan-view" class="{{ ($absensi && $absensi->kegiatan) ? '' : 'hidden' }}">
                <p id="display-kegiatan" class="text-gray-700 whitespace-pre-line leading-relaxed mb-3 p-3 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    {{ $absensi->kegiatan ?? '' }}
                </p>
                <button id="btn-edit-kegiatan" class="w-full bg-yellow-500 text-white py-3 rounded-xl font-semibold flex items-center justify-center">
                    ✏️ Edit Kegiatan
                </button>
            </div>

            {{-- EDIT MODE (Tampil jika kegiatan kosong atau saat klik tombol Edit) --}}
            <div id="kegiatan-form" class="{{ ($absensi && $absensi->kegiatan) ? 'hidden' : '' }}">
                <textarea
                    id="kegiatan"
                    rows="3"
                    maxlength="1000"
                    class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary-500"
                    placeholder="Tuliskan kegiatan hari ini...">{{ $absensi->kegiatan ?? '' }}</textarea>

                <button id="btn-simpan-kegiatan" class="mt-3 w-full bg-blue-600 text-white py-3 rounded-xl font-semibold">
                    💾 Simpan Kegiatan
                </button>
            </div>
        </div>







        @endif

    </div>

</div>



<!-- Loading Modal -->

<div id="loading-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">

    <div class="bg-white rounded-2xl p-6 mx-4 text-center">

        <div class="w-12 h-12 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div>

        <p class="mt-4 text-gray-600" id="loading-text">Memproses absensi...</p>

    </div>

</div>

@endsection



@push('scripts')

<script>
    // Config
    const CONFIG = {
        pkl: {
            lat: {{ $lokasi->latitude ?? 0 }},
            lng: {{ $lokasi->longitude ?? 0 }},
            radius: {{ $lokasi->radius ?? 100 }},
            nama: "{{ $lokasi->nama_tempat_pkl ?? '' }}"
        },
        sudahAbsenMasuk: {{ ($statusHariIni['sudah_absen_masuk'] ?? false) ? 'true' : 'false' }},
        sudahAbsenPulang: {{ ($statusHariIni['sudah_absen_pulang'] ?? false) ? 'true' : 'false' }}
    };

    let map, userMarker, schoolMarker, radiusCircle;
    let currentPosition = null;
    let capturedImage = null;
    let isWithinRadius = false;
    let cameraStream = null;

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', function() {
        initClock();
        initMap();
        initCamera();
        getLocation();
        updateButtonState();
        updateKegiatanVisibility();
        
        // Event Listeners
        document.getElementById('btn-capture')?.addEventListener('click', capturePhoto);
        document.getElementById('btn-retake')?.addEventListener('click', retakePhoto);
        document.getElementById('btn-masuk')?.addEventListener('click', () => submitAbsen('masuk'));
        document.getElementById('btn-pulang')?.addEventListener('click', () => submitAbsen('pulang'));
        document.getElementById('btn-simpan-kegiatan')?.addEventListener('click', simpanKegiatan);
        
        document.getElementById('btn-edit-kegiatan')?.addEventListener('click', () => {
            document.getElementById('kegiatan-view').classList.add('hidden');
            document.getElementById('kegiatan-form').classList.remove('hidden');
            document.getElementById('kegiatan').focus();
        });
    });

    // --- FUNCTIONS ---

    function initClock() {
        const updateClock = () => {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('header-clock');
            if(el) el.textContent = `${h}:${m}:${s}`;
        };
        setInterval(updateClock, 1000);
        updateClock();
    }

    function initMap() {
        if (!CONFIG.pkl.lat || !CONFIG.pkl.lng) return;
        map = L.map('map').setView([CONFIG.pkl.lat, CONFIG.pkl.lng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const schoolIcon = L.divIcon({
            html: '<div style="font-size: 24px;">🏫</div>',
            iconSize: [30, 30],
            className: 'school-marker'
        });
        
        schoolMarker = L.marker([CONFIG.pkl.lat, CONFIG.pkl.lng], { icon: schoolIcon })
            .addTo(map)
            .bindPopup(`<b>${CONFIG.pkl.nama}</b>`);

        radiusCircle = L.circle([CONFIG.pkl.lat, CONFIG.pkl.lng], {
            radius: CONFIG.pkl.radius,
            color: '#4f46e5',
            fillOpacity: 0.1,
            weight: 2
        }).addTo(map);
    }

    function getLocation() {
        if (!navigator.geolocation) {
            showLocationStatus('error', 'Browser tidak mendukung GPS');
            return;
        }

        showLocationStatus('loading', 'Mencari sinyal GPS...');

        navigator.geolocation.watchPosition(
            (position) => {
                currentPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                const userIcon = L.divIcon({
                    html: '<div style="font-size: 20px;">📱</div>',
                    iconSize: [24, 24],
                    className: 'user-marker'
                });

                if (userMarker) {
                    userMarker.setLatLng([currentPosition.lat, currentPosition.lng]);
                } else {
                    userMarker = L.marker([currentPosition.lat, currentPosition.lng], { icon: userIcon }).addTo(map);
                }

                const distance = map.distance(
                    [currentPosition.lat, currentPosition.lng], 
                    [CONFIG.pkl.lat, CONFIG.pkl.lng]
                );

                isWithinRadius = distance <= CONFIG.pkl.radius;

                if (isWithinRadius) {
                    showLocationStatus('success', `✅ Dalam radius (${Math.round(distance)}m)`);
                } else {
                    showLocationStatus('error', `❌ Di luar radius (${Math.round(distance)}m)`);
                }

                updateButtonState();
                
                const bounds = L.latLngBounds([
                    [currentPosition.lat, currentPosition.lng],
                    [CONFIG.pkl.lat, CONFIG.pkl.lng]
                ]);
                map.fitBounds(bounds, { padding: [30, 30] });
            },
            (error) => showLocationStatus('error', 'Gagal akses lokasi. Aktifkan GPS.'),
            { enableHighAccuracy: true }
        );
    }

    function showLocationStatus(type, message) {
        const el = document.getElementById('location-status');
        if(!el) return;
        const classes = {
            success: 'bg-green-50 border-green-200 text-green-700',
            error: 'bg-red-50 border-red-200 text-red-700',
            loading: 'bg-gray-100 border-gray-200 text-gray-600'
        };
        el.className = `mb-4 p-4 rounded-xl border ${classes[type]}`;
        el.innerHTML = `<span>${message}</span>`;
    }

    async function initCamera() {
        const video = document.getElementById('camera');
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' },
                audio: false
            });
            video.srcObject = cameraStream;
        } catch (err) {
            document.getElementById('camera-error').classList.remove('hidden');
        }
    }

    function capturePhoto() {
        const video = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        capturedImage = canvas.toDataURL('image/jpeg', 0.8);
        preview.src = capturedImage;
        
        video.classList.add('hidden');
        preview.classList.remove('hidden');
        document.getElementById('btn-capture').classList.add('hidden');
        document.getElementById('btn-retake').classList.remove('hidden');
        updateButtonState();
    }

    function retakePhoto() {
        capturedImage = null;
        document.getElementById('camera').classList.remove('hidden');
        document.getElementById('preview').classList.add('hidden');
        document.getElementById('btn-capture').classList.remove('hidden');
        document.getElementById('btn-retake').classList.add('hidden');
        updateButtonState();
    }

    function updateButtonState() {
        const canAbsen = isWithinRadius && capturedImage;
        const btnMasuk = document.getElementById('btn-masuk');
        const btnPulang = document.getElementById('btn-pulang');

        if (btnMasuk) {
            if (CONFIG.sudahAbsenMasuk) {
                btnMasuk.disabled = true;
                btnMasuk.innerHTML = '✅ Sudah Masuk';
            } else {
                btnMasuk.disabled = !canAbsen;
            }
        }

        if (btnPulang) {
            if (CONFIG.sudahAbsenPulang) {
                btnPulang.disabled = true;
                btnPulang.innerHTML = '✅ Sudah Pulang';
            } else if (!CONFIG.sudahAbsenMasuk) {
                btnPulang.disabled = true;
            } else {
                btnPulang.disabled = !canAbsen;
            }
        }
    }

    async function simpanKegiatan() {
        const kegiatanInput = document.getElementById('kegiatan');
        const text = kegiatanInput.value.trim();
        if (!text) return alert('Kegiatan tidak boleh kosong');

        const btn = document.getElementById('btn-simpan-kegiatan');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const response = await fetch('/siswa/absensi/kegiatan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ kegiatan: text })
            });
            const result = await response.json();
            if (result.success) {
                document.getElementById('display-kegiatan').innerText = text;
                document.getElementById('kegiatan-form').classList.add('hidden');
                document.getElementById('kegiatan-view').classList.remove('hidden');
                alert('✅ Kegiatan disimpan');
            }
        } catch (e) {
            alert('Gagal menyimpan kegiatan');
        } finally {
            btn.disabled = false;
            btn.textContent = '💾 Simpan Kegiatan';
        }
    }

    async function submitAbsen(type) {
        if (!currentPosition || !capturedImage) return alert('Lengkapi foto & lokasi!');
        
        const loader = document.getElementById('loading-modal');
        loader.classList.replace('hidden', 'flex');

        try {
            const response = await fetch('/siswa/absensi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tipe: type,
                    latitude: currentPosition.lat,
                    longitude: currentPosition.lng,
                    foto: capturedImage
                })
            });
            const res = await response.json();
            if (res.success) {
                window.location.reload();
            } else {
                alert(res.message);
            }
        } catch (e) {
            alert('Koneksi bermasalah');
        } finally {
            loader.classList.replace('flex', 'hidden');
        }
    }

    function updateKegiatanVisibility() {
        const wrapper = document.getElementById('kegiatan-wrapper');
        if (wrapper) {
            if (CONFIG.sudahAbsenMasuk && !CONFIG.sudahAbsenPulang) {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
            }
        }
    }
</script>

@endpush