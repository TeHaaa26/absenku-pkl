@extends('layouts.admin')

@section('title', 'Lokasi Sekolah')
@section('subtitle', 'Atur titik lokasi dan radius absensi')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Peta -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">📍 Peta Lokasi</h3>
                <p class="text-sm text-gray-500">Klik pada peta untuk memilih titik lokasi</p>
            </div>
            <div id="map" class="h-96"></div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">⚙️ Pengaturan Lokasi</h3>
            
            <form action="{{ route('admin.lokasi.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $lokasi->nama_sekolah ?? '') }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('nama_sekolah') border-red-500 @enderror"
                           placeholder="Nama sekolah">
                    @error('nama_sekolah')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                              placeholder="Alamat lengkap sekolah">{{ old('alamat', $lokasi->alamat ?? '') }}</textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $lokasi->latitude ?? '') }}" required readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 @error('latitude') border-red-500 @enderror">
                        @error('latitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $lokasi->longitude ?? '') }}" required readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 @error('longitude') border-red-500 @enderror">
                        @error('longitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Radius Absensi (meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius" id="radius" value="{{ old('radius', $lokasi->radius ?? 100) }}" min="10" max="1000" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('radius') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Jarak maksimal guru dapat melakukan absensi (10-1000 meter)</p>
                    @error('radius')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-semibold">
                        💾 Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @if($lokasi)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <p class="text-sm text-blue-700">
            <strong>Info:</strong> Guru dapat melakukan absensi dalam radius {{ $lokasi->radius }} meter dari titik lokasi sekolah.
        </p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi peta
    const defaultLat = {{ $lokasi->latitude ?? -6.200000 }};
    const defaultLng = {{ $lokasi->longitude ?? 106.816666 }};
    const defaultRadius = {{ $lokasi->radius ?? 100 }};

    const map = L.map('map').setView([defaultLat, defaultLng], 17);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;
    let circle = null;

    // Fungsi untuk set marker dan circle
    function setLocation(lat, lng) {
        const radius = parseInt(document.getElementById('radius').value) || 100;
        
        if (marker) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            circle.setRadius(radius);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            circle = L.circle([lat, lng], {
                radius: radius,
                color: '#4f46e5',
                fillColor: '#4f46e5',
                fillOpacity: 0.2
            }).addTo(map);
            
            // Event ketika marker di-drag
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(8);
                document.getElementById('longitude').value = pos.lng.toFixed(8);
            });
        }
        
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    }

    // Set lokasi awal jika sudah ada
    @if($lokasi)
    setLocation(defaultLat, defaultLng);
    @endif

    // Event klik pada peta
    map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    // Update radius ketika input berubah
    document.getElementById('radius').addEventListener('change', function() {
        if (circle) {
            circle.setRadius(parseInt(this.value) || 100);
        }
    });
</script>
@endpush