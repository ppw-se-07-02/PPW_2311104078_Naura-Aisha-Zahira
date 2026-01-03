<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SiswaController;
// use App\Http\Controllers\Admin\GuruController;
// use App\Http\Controllers\Admin\DataPenggunaController; // ✅ TAMBAHKAN INI
// use App\Http\Controllers\Admin\AdminDashboardController; // ✅ TAMBAHKAN INI
// use App\Http\Controllers\Admin\PresensiController; // ✅ TAMBAHKAN INI
// use App\Http\Controllers\Admin\LaporanEvaluasiController; // ✅ TAMBAHKAN INI
// use App\Http\Controllers\Guru\GuruDashboardController;
// use App\Http\Controllers\Guru\GuruProfilSiswaController; // ✅ TAMBAH INI
// use App\Http\Controllers\Guru\GuruPresensiController; // ✅ TAMBAH INI
// use App\Http\Controllers\Guru\GuruPerkembanganController; // ✅ TAMBAH INI
// use App\Http\Controllers\Guru\GuruLaporanEvaluasiController; // ✅ TAMBAH INI
// use App\Http\Controllers\Admin\RiwayatNotifikasiController;


// // ==========================================
// // 1. AUTHENTICATION (Login & Logout)
// // ==========================================
// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

// Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// // ==========================================
// // 2. ROLE GURU
// // ==========================================
// Route::prefix('guru')->name('guru.')->middleware('auth')->group(function () {
    
//     // ✅ Dashboard - Pakai Controller
//     Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

//     // ✅ PRESENSI - Pakai Controller sekarang
//     Route::get('/presensi', [GuruPresensiController::class, 'index'])->name('presensi');
//     Route::post('/presensi/store', [GuruPresensiController::class, 'store'])->name('presensi.store');
//     Route::post('/presensi/update-single', [GuruPresensiController::class, 'updateSingle'])->name('presensi.update_single');
//     Route::get('/presensi/by-date', [GuruPresensiController::class, 'getByDate'])->name('presensi.by_date');

//     // ✅ PROFIL SISWA - Pakai Controller sekarang
//     Route::get('/profil-siswa', [GuruProfilSiswaController::class, 'index'])->name('profil_siswa');
    
//     // ✅ AJAX Routes untuk Profil Siswa
//     Route::get('/profil-siswa/detail/{nis}', [GuruProfilSiswaController::class, 'getDetail'])->name('profil_siswa.detail');
//     Route::get('/profil-siswa/search', [GuruProfilSiswaController::class, 'search'])->name('profil_siswa.search');

//     // ✅ PERKEMBANGAN - Pakai Controller sekarang
//     Route::get('/perkembangan', [GuruPerkembanganController::class, 'index'])->name('perkembangan');
//     Route::post('/perkembangan/store', [GuruPerkembanganController::class, 'store'])->name('perkembangan.store');
//     Route::get('/perkembangan/by-date', [GuruPerkembanganController::class, 'getByDate'])->name('perkembangan.by_date');
//     Route::get('/perkembangan/detail', [GuruPerkembanganController::class, 'getDetail'])->name('perkembangan.detail');

//     // ✅ LAPORAN EVALUASI - Pakai Controller sekarang
//     Route::get('/laporan-evaluasi', [GuruLaporanEvaluasiController::class, 'index'])->name('laporan_evaluasi');
//     Route::get('/laporan-evaluasi/detail', [GuruLaporanEvaluasiController::class, 'getDetail'])->name('laporan_evaluasi.detail');
// });


// // ==========================================
// // 3. ROLE ADMIN
// // ==========================================
// Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    
//     // Dashboard Admin
//     Route::get('/dashboard-admin', function () {
//         $totalGuru = \App\Models\User::where('role', 'guru')->count();
//         $totalOrangTua = \App\Models\Siswa::distinct('no_hp')->count('no_hp');;
//         $totalSiswa = \App\Models\User::where('role', 'siswa')->count(); ;
//         $totalNotifikasi = 0;
        
//         $chartLabels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
//         $chartDatasets = [];
//         $kelasNames = [];
        
//         return view('admin.dashboard_admin', compact(
//             'totalGuru',
//             'totalOrangTua', 
//             'totalSiswa',
//             'totalNotifikasi',
//             'chartLabels',
//             'chartDatasets',
//             'kelasNames'
//         ));
//     })->name('dashboard_admin');

//     // ✅ GANTI INI - Pakai Controller
//     Route::get('/data-pengguna', [DataPenggunaController::class, 'index'])->name('data_pengguna');

//     //  ====== GURU ROUTES ======
//     Route::get('/data-pengguna/guru/tambah', function () {
//         return view('admin.data_pengguna.guru.tambah_guru');
//     })->name('guru.create');

//     Route::post('/data-pengguna/guru/tambah', [GuruController::class, 'store'])->name('guru.store');

//     Route::get('/data-pengguna/guru/edit/{id}', [GuruController::class, 'edit'])->name('guru.edit');

//     Route::get('/data-pengguna/guru/detail/{id}', 
//         [GuruController::class, 'show']
//     )->name('guru.show');


//     // ====== SISWA ROUTES ======
//     Route::get('/data-pengguna/siswa/tambah', function () {
//         return view('admin.data_pengguna.siswa.tambah_siswa');
//     })->name('siswa.create');
    
//     Route::post('/data-pengguna/siswa/tambah', [SiswaController::class, 'store'])->name('siswa.store');

//     // Hapus yang lama, ganti jadi ini:
//     Route::get('/data-pengguna/siswa/edit/{id}', [SiswaController::class, 'edit'])->name('siswa.edit');

//     Route::get('/data-pengguna/siswa/detail/{id}', 
//         [SiswaController::class, 'show']
//     )->name('siswa.show');
    
//     // Data Presensi
//     // ✅ BENAR - Pakai controller
//     Route::get('/data-presensi', 
//     [PresensiController::class, 'index']
//     )->name('data_presensi');

//     // ✅ LAPORAN EVALUASI - Ganti jadi pakai Controller
//     Route::get('/laporan-evaluasi', [LaporanEvaluasiController::class, 'index'])->name('laporan_evaluasi');
//     Route::get('/laporan-evaluasi/detail', [LaporanEvaluasiController::class, 'getDetail'])->name('laporan_evaluasi.detail');
//     Route::get('/laporan-evaluasi/by-class', [LaporanEvaluasiController::class, 'getByClassAndDate'])->name('laporan_evaluasi.by_class');

//     // Riwayat Notifikasi
//     Route::get('/riwayat-notifikasi', function () {
//         return view('admin.riwayat_notifikasi');
//     })->name('riwayat_notifikasi');

//     Route::post('/notifikasi/send', [RiwayatNotifikasiController::class, 'send'])
//     ->name('notifikasi.send');
//     Route::post('/notifikasi/resend', [RiwayatNotifikasiController::class, 'resend'])
//         ->name('notifikasi.resend');
//     Route::post('/notifikasi/resend-all', [RiwayatNotifikasiController::class, 'resendAll'])
//         ->name('notifikasi.resend_all');
// });

Route::prefix('teori')->group(function () {

    // INSERT DENGAN RAW SQL
    Route::get('/siswa/raw-sql', [SiswaController::class, 'storeRawSQL'])
        ->name('teori.siswa.raw');

    // INSERT DENGAN QUERY BUILDER
    Route::get('/siswa/query-builder', [SiswaController::class, 'storeQueryBuilder'])
        ->name('teori.siswa.qb');

    // INSERT DENGAN ELOQUENT ORM
    // (sudah ada di store(), ini hanya catatan)
    // Route::post('/siswa/eloquent', [SiswaController::class, 'store']);
});