<?php

use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\IzinController;
use App\Http\Controllers\Panel\JadwalAdminController;
use App\Http\Controllers\Panel\KelasAdminController;
use App\Http\Controllers\Panel\MapelAdminController;
use App\Http\Controllers\Panel\LoginController;
use App\Http\Controllers\Panel\PengumumanAdminController;
use App\Http\Controllers\Panel\RekapKelasController;
use App\Http\Controllers\Panel\SesiController;
use App\Http\Controllers\Panel\SiswaAdminController;
use App\Http\Controllers\Panel\StafAdminController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/panel');

// Dibutuhkan middleware 'auth' sebagai tujuan redirect tamu.
Route::get('/login', fn () => redirect()->route('panel.login'))->name('login');

Route::prefix('panel')->name('panel.')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login')->middleware('guest');
    Route::post('login', [LoginController::class, 'login'])->name('login.proses')->middleware('guest');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

    // Admin, guru, wali kelas
    Route::middleware(['auth', EnsureRole::class.':admin,guru,wali'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('sesi', [SesiController::class, 'index'])->name('sesi.index');
        Route::get('sesi/{sesi}', [SesiController::class, 'show'])->name('sesi.show');
        Route::post('sesi/{sesi}/koreksi/{siswa}', [SesiController::class, 'koreksi'])->name('sesi.koreksi');
        Route::post('sesi/{sesi}/alfa', [SesiController::class, 'tandaiAlfa'])->name('sesi.alfa');

        Route::get('rekap', [RekapKelasController::class, 'index'])->name('rekap');
    });

    // Admin dan guru: buka/tutup sesi
    Route::middleware(['auth', EnsureRole::class.':admin,guru'])->group(function () {
        Route::post('sesi/buka/{jadwal}', [SesiController::class, 'buka'])->name('sesi.buka');
        Route::post('sesi/{sesi}/tutup', [SesiController::class, 'tutup'])->name('sesi.tutup');
    });

    // Admin dan wali kelas: izin harian
    Route::middleware(['auth', EnsureRole::class.':admin,wali'])->group(function () {
        Route::get('izin', [IzinController::class, 'form'])->name('izin');
        Route::post('izin', [IzinController::class, 'store'])->name('izin.simpan');
    });

    // Admin saja
    Route::middleware(['auth', EnsureRole::class.':admin'])->group(function () {
        Route::get('jadwal', [JadwalAdminController::class, 'index'])->name('jadwal.index');
        Route::get('jadwal/tambah', [JadwalAdminController::class, 'create'])->name('jadwal.create');
        Route::post('jadwal', [JadwalAdminController::class, 'store'])->name('jadwal.store');
        Route::get('jadwal/{jadwal}/ubah', [JadwalAdminController::class, 'edit'])->name('jadwal.edit');
        Route::put('jadwal/{jadwal}', [JadwalAdminController::class, 'update'])->name('jadwal.update');
        Route::delete('jadwal/{jadwal}', [JadwalAdminController::class, 'destroy'])->name('jadwal.destroy');

        Route::get('pengumuman', [PengumumanAdminController::class, 'index'])->name('pengumuman.index');
        Route::post('pengumuman', [PengumumanAdminController::class, 'store'])->name('pengumuman.store');
        Route::delete('pengumuman/{pengumuman}', [PengumumanAdminController::class, 'destroy'])->name('pengumuman.destroy');

        // Data master
        Route::get('siswa', [SiswaAdminController::class, 'index'])->name('siswa.index');
        Route::get('siswa/tambah', [SiswaAdminController::class, 'create'])->name('siswa.create');
        Route::post('siswa', [SiswaAdminController::class, 'store'])->name('siswa.store');
        Route::get('siswa/{siswa}/ubah', [SiswaAdminController::class, 'edit'])->name('siswa.edit');
        Route::get('siswa/{siswa}/foto', [SiswaAdminController::class, 'foto'])->name('siswa.foto');
        Route::put('siswa/{siswa}', [SiswaAdminController::class, 'update'])->name('siswa.update');
        Route::delete('siswa/{siswa}', [SiswaAdminController::class, 'destroy'])->name('siswa.destroy');

        Route::get('staf', [StafAdminController::class, 'index'])->name('staf.index');
        Route::get('staf/tambah', [StafAdminController::class, 'create'])->name('staf.create');
        Route::post('staf', [StafAdminController::class, 'store'])->name('staf.store');
        Route::get('staf/{staf}/ubah', [StafAdminController::class, 'edit'])->name('staf.edit');
        Route::put('staf/{staf}', [StafAdminController::class, 'update'])->name('staf.update');
        Route::delete('staf/{staf}', [StafAdminController::class, 'destroy'])->name('staf.destroy');

        Route::get('kelas', [KelasAdminController::class, 'index'])->name('kelas.index');
        Route::post('kelas', [KelasAdminController::class, 'store'])->name('kelas.store');
        Route::get('kelas/{kelas}/ubah', [KelasAdminController::class, 'edit'])->name('kelas.edit');
        Route::put('kelas/{kelas}', [KelasAdminController::class, 'update'])->name('kelas.update');
        Route::delete('kelas/{kelas}', [KelasAdminController::class, 'destroy'])->name('kelas.destroy');

        Route::get('mapel', [MapelAdminController::class, 'index'])->name('mapel.index');
        Route::post('mapel', [MapelAdminController::class, 'store'])->name('mapel.store');
        Route::get('mapel/{mapel}/ubah', [MapelAdminController::class, 'edit'])->name('mapel.edit');
        Route::put('mapel/{mapel}', [MapelAdminController::class, 'update'])->name('mapel.update');
        Route::delete('mapel/{mapel}', [MapelAdminController::class, 'destroy'])->name('mapel.destroy');
    });
});
