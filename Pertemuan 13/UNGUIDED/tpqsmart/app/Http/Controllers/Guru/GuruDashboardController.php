<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    /**
     * Display dashboard untuk guru
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

        // 📊 STATISTIK DASHBOARD

        // 1. Total siswa di kelas yang diajar guru ini
        $totalSiswa = Siswa::where('kelas', $guru->kelas)->count();

        // 2. Siswa hadir hari ini (nanti dari tabel presensi)
        // Placeholder dulu karena belum ada tabel presensi
        $siswaHadirHariIni = 0;

        // 3. Persentase kehadiran
        $persentaseKehadiran = $totalSiswa > 0 
            ? round(($siswaHadirHariIni / $totalSiswa) * 100) 
            : 0;

        // 4. Siswa yang perlu evaluasi (placeholder)
        $siswaPerluEvaluasi = 0;

        // 5. Catatan perkembangan yang sudah diisi minggu ini (placeholder)
        $catatanPerkembanganMingguIni = 0;

        // 6. Aktivitas terbaru (placeholder - nanti dari tabel log/activity)
        $aktivitasTerbaru = [];

        // 7. List siswa di kelas untuk info tambahan
        $daftarSiswa = Siswa::where('kelas', $guru->kelas)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Kirim semua data ke view
        return view('guru.dashboard', compact(
            'guru',
            'totalSiswa',
            'siswaHadirHariIni',
            'persentaseKehadiran',
            'siswaPerluEvaluasi',
            'catatanPerkembanganMingguIni',
            'aktivitasTerbaru',
            'daftarSiswa'
        ));
    }

    /**
     * Get quick stats (untuk AJAX request kalau mau real-time)
     */
    public function getQuickStats()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['error' => 'Data guru tidak ditemukan'], 404);
        }

        $totalSiswa = Siswa::where('kelas', $guru->kelas)->count();
        $siswaHadirHariIni = 0; // Placeholder
        $persentaseKehadiran = $totalSiswa > 0 
            ? round(($siswaHadirHariIni / $totalSiswa) * 100) 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_siswa' => $totalSiswa,
                'siswa_hadir' => $siswaHadirHariIni,
                'persentase_kehadiran' => $persentaseKehadiran,
            ]
        ]);
    }
}