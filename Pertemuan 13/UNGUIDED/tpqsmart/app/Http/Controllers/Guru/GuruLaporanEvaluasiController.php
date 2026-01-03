<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Perkembangan;
use Carbon\Carbon;

class GuruLaporanEvaluasiController extends Controller
{
    /**
     * Display laporan evaluasi page
     */
    public function index(Request $request)
{
    $user = Auth::user();
    $guru = $user->guru;

    if (!$guru) {
        return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
    }

    // Get periode (default: hari ini)
    $selectedDate = $request->input('tanggal', Carbon::today()->toDateString());

    // Get semua siswa di kelas guru
    $siswaList = Siswa::where('kelas', $guru->kelas)
        ->orderBy('nama_lengkap', 'asc')
        ->get();

    // Prepare evaluasi data
    $evaluasiList = [];
    $countMelanjutkan = 0;
    $countMengulangi = 0;

    foreach ($siswaList as $siswa) {
        // Get perkembangan terakhir PADA TANGGAL YANG DIPILIH
        $perkembangan = Perkembangan::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $selectedDate)
            ->first();

        // Tentukan kemampuan
        $kemampuan = 'mengulangi'; // Default untuk yang belum ada data
        $tilawati = '-';

        if ($perkembangan) {
            $tilawati = $perkembangan->tilawati;
            
            if ($perkembangan->halaman) {
                $tilawati .= ' hal. ' . $perkembangan->halaman;
            }

            // Logika: Sangat Baik / Baik = Melanjutkan
            if (in_array($perkembangan->kemampuan, ['Sangat Baik', 'Baik'])) {
                $kemampuan = 'melanjutkan';
            }
        }

        // Count statistics
        if ($kemampuan === 'melanjutkan') {
            $countMelanjutkan++;
        } else {
            $countMengulangi++;
        }

        // ✅ SEMUA SISWA TETAP MASUK, meskipun belum ada data
        $evaluasiList[] = [
            'id' => $siswa->id,
            'student_id' => $siswa->nis,
            'student_name' => $siswa->nama_lengkap,
            'tilawati' => $tilawati,
            'kemampuan' => $kemampuan,
            'foto' => $siswa->foto,
            'has_data' => $perkembangan ? true : false, // ✅ Tambahan: indikator ada data atau tidak
        ];
    }

    return view('guru.laporan_evaluasi', compact(
        'guru',
        'evaluasiList',
        'countMelanjutkan',
        'countMengulangi',
        'selectedDate'
    ));
}
    /**
     * Get detail perkembangan siswa untuk modal
     */
    public function getDetail(Request $request)
    {
        $siswaId = $request->input('siswa_id');
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get siswa data
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas', $guru->kelas)
            ->first();

        if (!$siswa) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        // Get perkembangan pada tanggal yang dipilih
        $perkembangan = Perkembangan::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => [
                    'nis' => $siswa->nis,
                    'nama' => $siswa->nama_lengkap,
                    'foto' => $siswa->foto ? asset('storage/' . $siswa->foto) : null,
                ],
                'perkembangan' => $perkembangan ? [
                    'tilawati' => $perkembangan->tilawati . 
                        ($perkembangan->halaman ? ' halaman ' . $perkembangan->halaman : ''),
                    'kemampuan' => $perkembangan->kemampuan,
                    'hafalan' => $perkembangan->hafalan . 
                        ($perkembangan->ayat ? ' ' . $perkembangan->ayat : ''),
                    'tata_krama' => $perkembangan->tata_krama ?? '-',
                    'catatan' => $perkembangan->catatan ?? '-',
                ] : null,
            ]
        ]);
    }
}