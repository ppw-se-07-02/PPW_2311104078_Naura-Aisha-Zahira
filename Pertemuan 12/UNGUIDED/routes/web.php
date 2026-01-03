<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrangTuaController;

// ROUTE TANPA PARAMETER
// Route 1: Halaman Utama
Route::get('/', function () {
    return "Selamat Datang di TPQSmart - Sistem Manajemen TPQ Baitul Muttaqin";
});

// Route 2: Halaman Login
Route::get('/login', function () {
    return "Halaman Login TPQSmart";
});

// Route 3: Dashboard Admin
Route::get('/dashboard-admin', function () {
    return "Dashboard Admin TPQ Baitul Muttaqin";
});

// Route 4: Dashboard Guru
Route::get('/dashboard-guru', function () {
    return "Dashboard Guru TPQ Baitul Muttaqin";
});

// Route 5: Dashboard Orang Tua
Route::get('/dashboard-orangtua', function () {
    return "Dashboard Orang Tua/Wali Murid";
});


// ROUTE DENGAN PARAMETER
// Route 1: Lihat Profil Siswa Berdasarkan ID
Route::get('/siswa/{id}', function ($id) {
    return "Menampilkan Profil Siswa dengan ID: $id";
});

// Route 2: Lihat Presensi Berdasarkan Tanggal
Route::get('/presensi/{tanggal}', function ($tanggal) {
    return "Menampilkan Data Presensi Tanggal: $tanggal";
});

// Route 3: Detail Perkembangan Siswa
Route::get('/perkembangan/{id_siswa}', function ($id_siswa) {
    return "Menampilkan Perkembangan Siswa ID: $id_siswa";
});



// ROUTE DENGAN OPTIONAL PARAMETER 
// Route 1: Laporan Evaluasi (bulan dan tahun opsional)
Route::get('/laporan/{bulan?}/{tahun?}', function ($bulan = null, $tahun = null) {
    $bulan = $bulan ?? date('m');
    $tahun = $tahun ?? date('Y');
    return "Menampilkan Laporan Evaluasi Bulan: $bulan, Tahun: $tahun";
});

// Route 2: Data Siswa Per Kelas (kelas opsional)
Route::get('/data-siswa/{kelas?}', function ($kelas = 'Semua') {
    return "Menampilkan Data Siswa Kelas: $kelas";
});

// Route 3: Riwayat Presensi (bulan opsional)
Route::get('/riwayat-presensi/{id_siswa}/{bulan?}', function ($id_siswa, $bulan = null) {
    $bulan = $bulan ?? date('m');
    return "Riwayat Presensi Siswa ID: $id_siswa untuk Bulan: $bulan";
});



// Route untuk menampilkan view dashboard guru
Route::get('/view-dashboard-guru', function () {
    return view('dashboard-guru');
});


// Route untuk view presensi siswa
Route::get('/view-presensi', function () {
    return view('presensi-siswa');
});

// Route untuk perulangan FOR
Route::get('/blade-for', function () {
    return view('blade-for');
});

// Route untuk perulangan WHILE
Route::get('/blade-while', function () {
    return view('blade-while');
});

// Route untuk perulangan FOREACH
Route::get('/blade-foreach', function () {
    $nilai = [80, 64, 30, 76, 95];
    return view('blade-foreach', ['nilai' => $nilai]);
});



// Route 1: Halaman Utama/Beranda
Route::get('/', [PageController::class, 'index']);

// Route 2: Halaman Login
Route::get('/login', [PageController::class, 'login']);

// Route 3: Halaman About
Route::get('/about', [PageController::class, 'about']);


// Route 1: Dashboard Guru
Route::get('/dashboard-guru', [GuruController::class, 'dashboard']);

// Route 2: Profil Siswa (dengan parameter ID)
Route::get('/siswa/{id}', [GuruController::class, 'profilSiswa']);

// Route 3: Data Siswa (dengan optional parameter kelas)
Route::get('/data-siswa/{kelas?}', [GuruController::class, 'dataSiswa']);

// Route 4: Lihat Presensi (dengan parameter tanggal)
Route::get('/presensi/{tanggal}', [GuruController::class, 'lihatPresensi']);

// Route 5: Detail Perkembangan Siswa (dengan parameter id_siswa)
Route::get('/perkembangan/{id_siswa}', [GuruController::class, 'detailPerkembangan']);


// Route 1: Dashboard Admin
Route::get('/dashboard-admin', [AdminController::class, 'dashboard']);

// Route 2: Kelola Data Pengguna
Route::get('/data-pengguna', [AdminController::class, 'dataPengguna']);

// Route 3: Laporan Evaluasi (dengan optional parameter bulan dan tahun)
Route::get('/laporan/{bulan?}/{tahun?}', [AdminController::class, 'laporanEvaluasi']);

// Route 4: Kelola Data Presensi
Route::get('/kelola-presensi', [AdminController::class, 'kelolaPresensi']);


// Route 1: Dashboard Orang Tua
Route::get('/dashboard-orangtua', [OrangTuaController::class, 'dashboard']);

// Route 2: Riwayat Presensi Anak (dengan parameter id_siswa dan optional bulan)
Route::get('/riwayat-presensi/{id_siswa}/{bulan?}', [OrangTuaController::class, 'riwayatPresensi']);

// Route 3: Lihat Perkembangan Anak
Route::get('/lihat-perkembangan/{id_siswa}', [OrangTuaController::class, 'lihatPerkembangan']);

// Route 4: Profil Anak
Route::get('/profil-anak/{id_siswa}', [OrangTuaController::class, 'profilAnak']);


// Route untuk logout (bisa digunakan semua role)
Route::get('/logout', function () {
    return redirect('/login')->with('message', 'Anda telah berhasil logout');
});