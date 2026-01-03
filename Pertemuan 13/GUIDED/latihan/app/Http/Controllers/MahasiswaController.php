<?php
namespace App\Http\Controllers; use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

    class MahasiswaController extends Controller {
        public function insertData() {
            $mahasiswa = new Mahasiswa;
            $mahasiswa->nim = '19003036';
            $mahasiswa->nama_lengkap = 'Muhammad Hamada';
            $mahasiswa->tempat_lahir = 'Bandung';
            $mahasiswa->tanggal_lahir = '2002-02-02';
            $mahasiswa->alamat = 'J1. Merdeka No. 10';
            $mahasiswa->fakultas = 'Teknik';
            $mahasiswa->jurusan = 'Informatika';
            $mahasiswa->save();
            
        }
    }

    