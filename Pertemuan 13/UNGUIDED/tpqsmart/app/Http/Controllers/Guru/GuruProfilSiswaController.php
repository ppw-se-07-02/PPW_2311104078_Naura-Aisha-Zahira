<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use Carbon\Carbon;

class GuruProfilSiswaController extends Controller
{
    /**
     * Display profil siswa page
     */
    public function index()
    {
        // Ambil user yang login
        $user = Auth::user();

        // Ambil data guru dari relasi
        $guru = $user->guru;

        // Kalau user ini bukan guru atau belum punya data guru
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        // Ambil semua siswa di kelas yang diajar guru ini
        $siswaList = Siswa::where('kelas', $guru->kelas)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Transform data untuk JavaScript
        $siswaData = $siswaList->map(function($siswa) {
            return [
                'id' => $siswa->nis, // Pakai NIS sebagai ID
                'nama' => $siswa->nama_lengkap,
                'photo' => $siswa->foto 
                    ? asset('storage/' . $siswa->foto) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=2EAF7D&color=fff&size=200',
                'gender' => $siswa->jenis_kelamin,
                'tanggalLahir' => Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMMM YYYY'),
                'umur' => Carbon::parse($siswa->tanggal_lahir)->age . ' Tahun',
                'orangTua' => $this->extractNamaOrangTua($siswa->no_hp), // Extract dari database atau bisa tambah field
                'nomorWA' => $siswa->no_hp,
                'alamat' => $siswa->alamat,
            ];
        });

        // Kirim ke view
        return view('guru.profil_siswa', [
            'guru' => $guru,
            'siswaList' => $siswaList,
            'siswaData' => $siswaData,
            'totalSiswa' => $siswaList->count()
        ]);
    }

    /**
     * Get detail siswa (AJAX)
     */
    public function getDetail($nis)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil siswa berdasarkan NIS dan pastikan di kelas guru ini
        $siswa = Siswa::where('nis', $nis)
            ->where('kelas', $guru->kelas)
            ->first();

        if (!$siswa) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'photo' => $siswa->foto 
                    ? asset('storage/' . $siswa->foto) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=2EAF7D&color=fff&size=200',
                'gender' => $siswa->jenis_kelamin,
                'tanggalLahir' => Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMMM YYYY'),
                'umur' => Carbon::parse($siswa->tanggal_lahir)->age . ' Tahun',
                'orangTua' => $this->extractNamaOrangTua($siswa->no_hp),
                'nomorWA' => $siswa->no_hp,
                'alamat' => $siswa->alamat,
            ]
        ]);
    }

    /**
     * Helper: Extract nama orang tua dari field yang ada
     * (Kalau belum ada field nama_orang_tua, return placeholder)
     */
    private function extractNamaOrangTua($no_hp)
    {
        // TODO: Nanti kalau ada field nama_orang_tua di tabel siswas, ganti ini
        // Untuk sementara return "Orang Tua [nama siswa]"
        return "Wali Siswa"; // Placeholder
    }

    /**
     * Search siswa (AJAX)
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $searchTerm = $request->input('q', '');

        $siswaList = Siswa::where('kelas', $guru->kelas)
            ->where(function($query) use ($searchTerm) {
                $query->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('nis', 'LIKE', "%{$searchTerm}%");
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $siswaData = $siswaList->map(function($siswa) {
            return [
                'id' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'photo' => $siswa->foto 
                    ? asset('storage/' . $siswa->foto) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=2EAF7D&color=fff&size=200',
                'gender' => $siswa->jenis_kelamin,
                'tanggalLahir' => Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMMM YYYY'),
                'umur' => Carbon::parse($siswa->tanggal_lahir)->age . ' Tahun',
                'orangTua' => $this->extractNamaOrangTua($siswa->no_hp),
                'nomorWA' => $siswa->no_hp,
                'alamat' => $siswa->alamat,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $siswaData,
            'total' => $siswaList->count()
        ]);
    }
}