<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Controller Guru yang SUDAH ADA (tinggal pakai ulang!)
use App\Http\Controllers\Guru\GuruDashboardController;
use App\Http\Controllers\Guru\GuruProfilSiswaController;
use App\Http\Controllers\Guru\GuruPresensiController;
use App\Http\Controllers\Guru\GuruPerkembanganController;

// ✅ TAMBAHKAN IMPORT INI untuk Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DataPenggunaController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\LaporanEvaluasiController;

// Controller Ortu yang BARU (karena belum ada)
// use App\Http\Controllers\Ortu\OrtuDashboardController;
// use App\Http\Controllers\Ortu\OrtuPresensiController;
// use App\Http\Controllers\Ortu\OrtuPerkembanganController;
// use App\Http\Controllers\Ortu\OrtuProfilSiswaController;

/*
|--------------------------------------------------------------------------
| API Routes - TPQSmart Mobile App
|--------------------------------------------------------------------------
*/


// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'TPQSmart API v1.0',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Login API
Route::post('/login', [AuthController::class, 'loginApi']);

// Logout
Route::post('/logout', [AuthController::class, 'logoutApi']);

// Check Auth User
Route::get('/me', function (Request $request) {
    return response()->json([
        'success' => true,
        'user' => $request->user()
    ]);
});

    // ==========================================
    // 2. ADMIN ROUTES - SESUAI web.php
    // ==========================================
    Route::prefix('admin')->name('api.admin.')->group(function () {
        
    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    
    // Data Pengguna
    Route::get('/data-pengguna', [DataPenggunaController::class, 'index']);
    
    // GURU - TANPA DELETE (sesuai web.php)
    Route::get('/guru', [GuruController::class, 'index']); // List
    Route::get('/guru/{id}', [GuruController::class, 'show']); // Detail
    Route::post('/guru/store', [GuruController::class, 'store']); // Tambah
    Route::put('/guru/update/{id}', [GuruController::class, 'update']); // Update
    
    // SISWA - TANPA DELETE (sesuai web.php)
    Route::get('/siswa', [SiswaController::class, 'index']); // List
    Route::get('/siswa/{id}', [SiswaController::class, 'show']); // Detail
    Route::post('/siswa/store', [SiswaController::class, 'store']); // Tambah
    Route::put('/siswa/update/{id}', [SiswaController::class, 'update']); // Update
    
    // Data Presensi (Read Only)
    Route::get('/data-presensi', [PresensiController::class, 'index']);
    
    // Laporan Evaluasi (Read Only)
    Route::get('/laporan-evaluasi', [LaporanEvaluasiController::class, 'index']);
    Route::get('/laporan-evaluasi/detail', [LaporanEvaluasiController::class, 'getDetail']);
    Route::get('/laporan-evaluasi/by-class', [LaporanEvaluasiController::class, 'getByClassAndDate']);
    });



    // ==========================================
    // 3. ORANG TUA ROUTES
    // ==========================================
    // Route::prefix('ortu')->name('api.ortu.')->group(function () {
        
    //     // Dashboard - Ringkasan anak
    //     Route::get('/dashboard', [OrtuDashboardController::class, 'dashboard']);
        
    //     // Profil Anak
    //     Route::get('/profil-anak', [OrtuProfilSiswaController::class, 'index']);
        
    //     // Presensi Anak
    //     Route::get('/presensi', [OrtuPresensiController::class, 'index']);
    //     Route::get('/presensi/by-date', [OrtuPresensiController::class, 'getByDateRange']);
        
    //     // Perkembangan Anak
    //     Route::get('/perkembangan', [OrtuPerkembanganController::class, 'index']);
    //     Route::get('/perkembangan/by-date', [OrtuPerkembanganController::class, 'getByDateRange']);
        
    //     // Evaluasi Anak
    //     Route::get('/evaluasi', [OrtuPerkembanganController::class, 'getEvaluasi']);
    // });


    // ==========================================
    // 4. GURU ROUTES - PAKAI CONTROLLER YANG SUDAH ADA!
    // ==========================================
    Route::prefix('guru')->name('api.guru.')->group(function () {
        
        // Dashboard - PAKAI YANG SUDAH ADA
        Route::get('/dashboard', [GuruDashboardController::class, 'index']);
        
        // Profil Siswa - PAKAI YANG SUDAH ADA
        Route::get('/profil-siswa', [GuruProfilSiswaController::class, 'index']);
        Route::get('/profil-siswa/detail/{nis}', [GuruProfilSiswaController::class, 'getDetail']);
        Route::get('/profil-siswa/search', [GuruProfilSiswaController::class, 'search']);
        
        // Presensi - PAKAI YANG SUDAH ADA
        Route::get('/presensi', [GuruPresensiController::class, 'index']);
        Route::post('/presensi/store', [GuruPresensiController::class, 'store']);
        Route::post('/presensi/update-single', [GuruPresensiController::class, 'updateSingle']);
        Route::get('/presensi/by-date', [GuruPresensiController::class, 'getByDate']);
        
        // Perkembangan - PAKAI YANG SUDAH ADA
        Route::get('/perkembangan', [GuruPerkembanganController::class, 'index']);
        Route::post('/perkembangan/store', [GuruPerkembanganController::class, 'store']);
        Route::get('/perkembangan/by-date', [GuruPerkembanganController::class, 'getByDate']);
        Route::get('/perkembangan/detail', [GuruPerkembanganController::class, 'getDetail']);
        
        // Laporan Evaluasi - PAKAI YANG SUDAH ADA
        // Route::get('/laporan-evaluasi', [LaporanEvaluasiController::class, 'index']);
        // Route::post('/laporan-evaluasi/store', [LaporanEvaluasiController::class, 'store']);
        // Route::get('/laporan-evaluasi/{id}', [LaporanEvaluasiController::class, 'show']);
        // Route::put('/laporan-evaluasi/{id}', [LaporanEvaluasiController::class, 'update']);
        // Route::delete('/laporan-evaluasi/{id}', [LaporanEvaluasiController::class, 'destroy']);
    });
