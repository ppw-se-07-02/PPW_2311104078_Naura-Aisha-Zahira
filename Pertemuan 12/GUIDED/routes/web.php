<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController; 

Route::get('/', function () {
    return view('welcome');
});

Membuat route
Route::get('/beranda', function () {
    return "Halaman Beranda";
});

// Route parameter 
Route::get('/kendaraan/{jenis}', function ($jenis) {
    return "Tampilkan data kendaraan dengan jenis $jenis";
});

// Route dengan optional parameter
Route::get('/kendaraan/{jenis?}/{merek?}', function ($a = 'motor',$b = 'honda') {
    return "Cek harga kendaraan $a $b";
});

// Route parameter dengan regular expression
Route::get('/product/{id}', function ($id) {
    return "Tampilkan product dengan id = $id";
});

Route::get('/product/{id}', function ($id) {
    return "Tampilkan product dengan id = $id";
})->where('id', '[0-9]+');

// Route redirect
Route::get('/hubungi-kami', function () { 
    return '<h1>Hubungi Kami</h1>'; 
}); 
Route::redirect('/contact-us', '/hubungi-kami'); ;

// Route group
Route::prefix('/admin')->group(function() { 
    Route::get('/dashboard', function() { 
        return 'Tampilkan dashboard aplikasi'; 
    }); 
    Route::get('/datapegawai', function() { 
        return 'Tampilkan data pegawai'; 
    }); 
    Route::get('/datamahasiswa', function() { 
        return 'Tampilkan data mahasiswa'; 
    }); 
}); 

// Route fallback
Route::fallback(function () {
    return "Maaf, alamat tidak ditemukan";
});

// Route priority
Route::get('/baju/1', function () {
    return "Baju ke-1";
});
Route::get('/baju/1', function () {
    return "Baju saya ke-1";
});
Route::get('/baju/1', function () {
    return "Baju kita ke-1";
});

// Route priority menggunakan router parameter
Route::get('/baju/{a}', function ($a) {
    return "Baju ke-$a";
});
Route::get('/baju/{b}', function ($b) {
    return "Baju saya ke-$b";
});
Route::get('/baju/{c}', function ($c) {
    return "Baju kita ke-$c";
});


// membuat view
Route::get('/mahasiswa', function () { 
    return view('mahasiswa'); 
});

Route::get('/mahasiswa', function () { 
    return view('universitas/mahasiswa'); 
}); 

Route::get('/mahasiswa', function () { 
    return view('universitas.mahasiswa',["mhs1"=>"Aulia Jasifa"]); 
}); 

Route::get('/mahasiswa', function () { 
    return view('universitas.mahasiswa', 
    [ 
        "mhs1" => "Aulia Jasifa", 
        "mhs2" => "Naura Aisha", 
        "mhs3" => "Alya Rabani", 
        "mhs4" => "Berlian Seva" 
    ]); 
}); 

Route::get('/mahasiswa', function () {
    $arrMhs = [
        "mhs1" => "Aulia Jasifa",
        "mhs2" => "Naura Aisha",
        "mhs3" => "Alya Rabani",
        "mhs4" => "Berlian Seva"
    ];
    return view('universitas.mahasiswa', $arrMhs);
});

Route::get('/mahasiswa', function () {
    $arrMhs = ["Aulia Jasifa","Naura Aisha","Alya Rabani","Berlian Seva"];
    return view('universitas.mahasiswa',['mahasiswa' => $arrMhs]);
});

Route::get('/mahasiswa', function () {
    $arrMhs = ["Aulia jasifa","Naura Aisha","Alya Rabani","Berlian Seva"];
    return view('universitas.mahasiswa')->with('mahasiswa',
$arrMhs);
});

Route::get('/produk', function () {
    $arrProduk = [
        "prod1" => "Televisi",
        "prod2" => "Kipas Angin",
        "prod3" => "Radio"
    ];
    return view('produk', $arrProduk);
});

Route::get('/', [App\Http\Controllers\PageController::class,'index']); 
Route::get('/mahasiswa',[App\Http\Controllers\PageController::class,'tampil']); 