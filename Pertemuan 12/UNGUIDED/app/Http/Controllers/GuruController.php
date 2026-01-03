<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Dashboard Guru
     */
    public function dashboard()
    {
        $data = [
            'nama_guru' => 'Bu Chae',
            'kelas' => 'Kelas A',
            'tahun_ajaran' => '2025-2026',
            'jumlah_siswa' => 25
        ];
        
        return view('dashboard-guru', $data);
    }

    /**
     * Profil siswa berdasarkan ID
     */
    public function profilSiswa($id)
    {
        // Simulasi data siswa (nanti dari database)
        $siswa = [
            'id_siswa' => $id,
            'nama' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2010-05-15',
            'umur' => 15,
            'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            'nama_wali' => 'Bapak Fauzi',
            'no_hp_wali' => '081234567890',
            'kelas' => 'A'
        ];

        return view('profil-siswa', $siswa);
    }

    /**
     * Data siswa per kelas (kelas opsional)
     */
    public function dataSiswa($kelas = 'Semua')
    {
        // Simulasi data siswa
        $allSiswa = [
            ['id' => 1, 'nama' => 'Ahmad Fauzi', 'kelas' => 'A', 'umur' => 15],
            ['id' => 2, 'nama' => 'Siti Aminah', 'kelas' => 'A', 'umur' => 14],
            ['id' => 3, 'nama' => 'Muhammad Rizki', 'kelas' => 'B', 'umur' => 13],
            ['id' => 4, 'nama' => 'Fatimah Zahra', 'kelas' => 'B', 'umur' => 14],
            ['id' => 5, 'nama' => 'Ali Hasan', 'kelas' => 'C', 'umur' => 15],
        ];

        // Filter berdasarkan kelas jika bukan 'Semua'
        if ($kelas != 'Semua') {
            $siswa = array_filter($allSiswa, function($s) use ($kelas) {
                return $s['kelas'] == $kelas;
            });
        } else {
            $siswa = $allSiswa;
        }

        return view('data-siswa', [
            'siswa' => $siswa,
            'kelas_filter' => $kelas
        ]);
    }
}