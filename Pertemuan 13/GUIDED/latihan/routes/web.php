<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController; 

Route::get('/insert-data', [MahasiswaController::class,'insertdata']); 
Route::get('/select-data', [MahasiswaController::class,'selectData']); 
Route::get('/update-data', [MahasiswaController::class,'updateData']); 
Route::get('/delete-data', [MahasiswaController::class,'deleteData']);

// Route::get('/', function () {
//     return view('welcome');
// });

