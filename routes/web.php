<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

// Namespace Controller Siswa (Pindahkan dari folder Guru ke Siswa)
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\RiwayatController as SiswaRiwayatController;
use App\Http\Controllers\Siswa\IzinController as SiswaIzinController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;

// Namespace Controller Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\IzinController as AdminIzinController;
use App\Http\Controllers\Admin\PenempatanController as AdminPenempatanController;
use App\Http\Controllers\Admin\LokasiController as AdminLokasiController;
use App\Http\Controllers\Admin\JamKerjaController as AdminJamKerjaController;
use App\Http\Controllers\Admin\HariLiburController as AdminHariLiburController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;

use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\IzinController as GuruIzinController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\LaporanController as GuruLaporanController;
use App\Http\Controllers\Guru\SiswaController as GuruSiswaController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Redirect Utama Berdasarkan Role
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    // Cek Guard Guru dulu
    if (Auth::guard('guru')->check()) {
        return redirect()->route('guru.dashboard');
    }

    // Cek Guard Web (Admin/Siswa)
    if (Auth::check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->role === 'siswa') {
            return redirect()->route('siswa.dashboard');
        }
    }

    return redirect()->route('login');
});

// Post simpan kegiatan (Siswa)
Route::post('/siswa/absensi/kegiatan', [SiswaAbsensiController::class, 'simpanKegiatan'])
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Auth Routes (Login & Logout)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Cari baris ini dan ubah menjadi:
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Siswa Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [SiswaAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [SiswaAbsensiController::class, 'store'])->name('absensi.store');
    Route::post('/absensi/cek-lokasi', [SiswaAbsensiController::class, 'cekLokasi'])->name('absensi.cek-lokasi');

    // Riwayat
    Route::get('/riwayat', [SiswaRiwayatController::class, 'index'])->name('riwayat.index');

    // Izin
    Route::get('/izin', [SiswaIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [SiswaIzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [SiswaIzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/{id}', [SiswaIzinController::class, 'show'])->name('izin.show');

    // Profil
    Route::get('/profil', [SiswaProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [SiswaProfilController::class, 'updatePassword'])->name('profil.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola Siswa
    Route::resource('siswa', AdminSiswaController::class);

    // kelola guru
    Route::resource('guru', AdminGuruController::class);

    // Absensi & Izin
    Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{id}', [AdminAbsensiController::class, 'show'])->name('absensi.show');
    Route::get('/izin', [AdminIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/{id}', [AdminIzinController::class, 'show'])->name('izin.show');
    Route::post('/izin/{id}/approve', [AdminIzinController::class, 'approve'])->name('izin.approve');

    // Pengaturan (Lokasi, Jam Kerja, Hari Libur)
    Route::resource('lokasi', AdminLokasiController::class)->except(['create', 'show']);
    Route::resource('jam-kerja', AdminJamKerjaController::class)->except(['create', 'show']);
    Route::resource('hari-libur', AdminHariLiburController::class)->only(['index', 'store', 'destroy']);

    // Laporan
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    // penempatan
    Route::resource('penempatan-pkl', AdminPenempatanController::class);
});
/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/izin', [GuruIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/{id}', [GuruIzinController::class, 'show'])->name('izin.show');
    Route::post('/izin/{id}/approve', [GuruIzinController::class, 'approve'])->name('izin.approve');

    Route::get('/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{id}', [GuruAbsensiController::class, 'show'])->name('absensi.show');

    Route::get('/laporan', [GuruLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [GuruLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/excel', [GuruLaporanController::class, 'exportExcel'])->name('laporan.export-excel');

    Route::get('/siswa', [GuruSiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{id}', [GuruSiswaController::class, 'show'])->name('siswa.show');
});
