<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\RekapController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/presensi/aktif', [PresensiController::class, 'aktif']);
    Route::post('/presensi', [PresensiController::class, 'store']);

    Route::get('/jadwal', [JadwalController::class, 'index']);
    Route::get('/kalender', [JadwalController::class, 'kalender']);
    Route::get('/rekap', [RekapController::class, 'index']);
    Route::get('/pengumuman', [PengumumanController::class, 'index']);
});
