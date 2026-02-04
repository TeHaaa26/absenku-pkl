@extends('layouts.admin')

@section('title', 'Manajemen Lokasi PKL')
@section('subtitle', 'Kelola daftar titik lokasi dan radius absensi')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-semibold text-gray-800 mb-1" id="form-title">⚙️ Tambah Lokasi</h3>
                <p class="text-xs text-gray-500 mb-4">Klik pada peta untuk mengambil koordinat otomatis</p>

                <form action="{{ route('admin.lokasi.store') }}" method="POST" id="lokasiForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi/Pkl</label>
                        <input type="text" name="nama_tempat_pkl" id="nama_tempat_pkl" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 shadow-sm"
                            placeholder="Contoh: Kampus Utama">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-xl resize-none shadow-sm" placeholder="Alamat lengkap..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Latitude</label>
                            <input type="text" name="latitude" id="latitude" readonly required
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Longitude</label>
                            <input type="text" name="longitude" id="longitude" readonly required
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                        <input type="number" name="radius" id="radius" value="100" min="10" max="1000" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shift / Jam Kerja</label>
                        <select name="jam_kerja_id" id="jam_kerja_id"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 shadow-sm bg-white outline-none">
                            <option value="">-- Pilih Jam Kerja (Opsional) --</option>
                            @foreach($jamKerjas as $jk)
                            <option value="{{ $jk->id }}">
                                {{ $jk->nama_shift }} ({{ \Carbon\Carbon::parse($jk->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($jk->jam_pulang)->format('H:i') }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 flex flex-col gap-2">
                        <button type="submit" class="w-full py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md transition-all">
                            💾 Simpan Lokasi
                        </button>
                        <button type="button" onclick="resetForm()" id="btn-reset" class="hidden w-full py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-medium transition-all">
                            Batal Edit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div id="map" class="h-80 w-full"></div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="font-bold text-gray-800">Daftar Lokasi Terdaftar</h3>
                    
                    <form method="GET" action="{{ route('admin.lokasi.index') }}" class="flex gap-2 w-full sm:w-auto">
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama atau alamat..." 
                                class="w-full sm:w-64 pl-9 pr-4 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('admin.lokasi.index') }}" class="p-1.5 bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200" title="Bersihkan">
                                ✕
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-1.5 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 font-medium transition-colors">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 text-sm">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Nama & Alamat</th>
                                <th class="px-6 py-3 font-semibold text-center">Radius</th>
                                <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lokasi as $l)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-bold text-gray-800">{{ $l->nama_tempat_pkl }}</div>
                                    <div class="text-gray-500 truncate max-w-xs">{{ $l->alamat ?? '-' }}</div>
                                    <div class="mt-1 flex items-center gap-1 text-[11px]">
                                        <span class="text-primary-600 font-medium">🕒 Shift:</span>
                                        <span class="text-gray-600">
                                            {{ $l->jamKerja->nama_shift ?? 'Belum diatur' }}
                                            @if($l->jamKerja)
                                            ({{ \Carbon\Carbon::parse($l->jamKerja->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($l->jamKerja->jam_pulang)->format('H:i') }})
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-primary-50 text-primary-600 rounded-full text-xs font-bold">
                                        {{ $l->radius }}m
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick='editLokasi(@json($l))' class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                            ✏️
                                        </button>
                                        <form action="{{ route('admin.lokasi.destroy', $l->id) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-400">
                                    {{ request('search') ? 'Lokasi tidak ditemukan.' : 'Belum ada lokasi yang ditambahkan.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Koordinat Default
    const defaultCenter = [-6.555700, 107.759800]; // Koordinat Subang sesuai gambar
    const map = L.map('map').setView(defaultCenter, 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let activeMarker = null;
    let activeCircle = null;

    // 1. Tampilkan SEMUA lokasi yang sudah tersimpan dengan radiusnya
    const locations = @json($lokasi);
    locations.forEach(loc => {
        // Tambah Marker
        L.marker([loc.latitude, loc.longitude]).addTo(map)
            .bindPopup(`<b>${loc.nama_tempat_pkl}</b><br>Radius: ${loc.radius}m`);

        // Tambah Lingkaran Radius (Warna Biru seperti di gambar)
        L.circle([loc.latitude, loc.longitude], {
            color: '#4f46e5', // Warna garis luar
            fillColor: '#4f46e5', // Warna isi
            fillOpacity: 0.2, // Transparansi isi
            radius: loc.radius // Jarak dalam meter
        }).addTo(map);
    });

    // 2. Fungsi untuk Marker Input Baru / Edit
    function updateInputMarker(lat, lng) {
        const rad = parseInt(document.getElementById('radius').value) || 100;

        if (activeMarker) {
            activeMarker.setLatLng([lat, lng]);
            activeCircle.setLatLng([lat, lng]);
            activeCircle.setRadius(rad);
        } else {
            // Marker yang sedang dipilih (Warna Hijau agar beda)
            activeMarker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            activeCircle = L.circle([lat, lng], {
                radius: rad,
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.3
            }).addTo(map);

            activeMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(8);
                document.getElementById('longitude').value = pos.lng.toFixed(8);
                activeCircle.setLatLng(pos);
            });
        }

        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    }

    // Klik Peta untuk pilih lokasi
    map.on('click', function(e) {
        updateInputMarker(e.latlng.lat, e.latlng.lng);
    });

    // Update radius secara real-time saat angka di input diubah
    document.getElementById('radius').addEventListener('input', function() {
        if (activeCircle) {
            activeCircle.setRadius(parseInt(this.value) || 0);
        }
    });

    // Fungsi Edit (dipanggil dari tombol tabel)
    function editLokasi(data) {
        document.getElementById('form-title').innerText = "✏️ Edit Lokasi";
        document.getElementById('form-method').value = "PUT";
        document.getElementById('lokasiForm').action = "{{ url('admin/lokasi') }}/" + data.id;

        document.getElementById('nama_tempat_pkl').value = data.nama_tempat_pkl;
        document.getElementById('alamat').value = data.alamat;
        document.getElementById('radius').value = data.radius;
        document.getElementById('jam_kerja_id').value = data.jam_kerja_id || "";
        document.getElementById('btn-reset').classList.remove('hidden');

        // Mengisi input text koordinat
        document.getElementById('latitude').value = data.latitude;
        document.getElementById('longitude').value = data.longitude;

        // Sinkronisasi Marker dan Lingkaran di Peta
        const lat = parseFloat(data.latitude);
        const lng = parseFloat(data.longitude);
        updateInputMarker(lat, lng);
        map.setView([lat, lng], 17);
    }

    function resetForm() {
        location.reload(); // Reset paling aman untuk membersihkan peta
    }
</script>
@endpush