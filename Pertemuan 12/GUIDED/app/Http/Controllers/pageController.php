<?php 
namespace App\Http\Controllers; 

class PageController extends Controller 
{ 
    public function index() { 
        return "Halaman Home"; 
    } 
    public function tampil() { 
        $arrMahasiswa = ["Aulia Jasifa","Naura Aisha","Alya Rabani","Berlian Seva"]; 
        return view('mahasiswa')->with('mahasiswa', $arrMahasiswa); 
    } 
} 