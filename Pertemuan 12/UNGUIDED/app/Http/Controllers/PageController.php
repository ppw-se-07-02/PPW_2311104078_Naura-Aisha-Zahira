<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Halaman utama/beranda
     */
    public function index()
    {
        return view('welcome-tpq');
    }

    /**
     * Halaman login
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Halaman about
     */
    public function about()
    {
        $data = [
            'nama_tpq' => 'TPQ Baitul Muttaqin',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'tahun_berdiri' => '2010',
            'jumlah_santri' => 150,
            'jumlah_guru' => 8
        ];
        
        return view('about', $data);
    }
}