<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Perkembangan;
use Carbon\Carbon;

class LaporanEvaluasiController extends Controller
{
    /**
     * Display laporan evaluasi page for admin
     */
    public function index(Request $request)
    {
        try {
            // Get parameters
            $selectedClass = $request->input('kelas', null);
            $selectedDate = $request->input('tanggal', Carbon::today()->toDateString());

            // Get all unique classes from siswa table
            $classList = Siswa::select('kelas', DB::raw('COUNT(*) as students_count'))
                ->whereNotNull('kelas')
                ->groupBy('kelas')
                ->orderBy('kelas', 'asc')
                ->get()
                ->map(function($item) {
                    return (object)[
                        'id' => $item->kelas,
                        'nama' => $item->kelas,
                        'students_count' => $item->students_count
                    ];
                });

            // Initialize data
            $evaluasiData = [];
            $totalStudents = 0;
            $countMelanjutkan = 0;
            $countMengulangi = 0;

            // If class is selected, get evaluation data
            if ($selectedClass) {
                // Get all students in the class
                $siswaList = Siswa::where('kelas', $selectedClass)
                    ->orderBy('nama_lengkap', 'asc')
                    ->get();

                $totalStudents = $siswaList->count();

                foreach ($siswaList as $siswa) {
                    // Get perkembangan for selected date
                    $perkembangan = Perkembangan::where('siswa_id', $siswa->id)
                        ->whereDate('tanggal', $selectedDate)
                        ->first();

                    // Determine kemampuan
                    $kemampuan = 'mengulangi'; // Default
                    $tilawati = '-';

                    if ($perkembangan) {
                        $tilawati = $perkembangan->tilawati;
                        
                        if ($perkembangan->halaman) {
                            $tilawati .= ' hal. ' . $perkembangan->halaman;
                        }

                        // Logic: Sangat Baik / Baik = Melanjutkan
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

                    $evaluasiData[] = (object)[
                        'id' => $siswa->id,
                        'student_id' => $siswa->nis,
                        'student_name' => $siswa->nama_lengkap,
                        'tilawati' => $tilawati,
                        'kemampuan' => $kemampuan,
                        'foto' => $siswa->foto,
                    ];
                }
            }

            return view('admin.laporan_evaluasi', compact(
                'classList',
                'selectedClass',
                'selectedDate',
                'evaluasiData',
                'totalStudents',
                'countMelanjutkan',
                'countMengulangi'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in admin laporan evaluasi:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.dashboard_admin')
                ->with('error', 'Terjadi kesalahan saat memuat data evaluasi!');
        }
    }

    /**
     * Get detail perkembangan siswa untuk modal (AJAX)
     */
    public function getDetail(Request $request)
    {
        try {
            $siswaId = $request->input('siswa_id');
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

            // Get siswa data
            $siswa = Siswa::findOrFail($siswaId);

            // Get perkembangan on selected date
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get evaluasi data by class and date (AJAX)
     */
    public function getByClassAndDate(Request $request)
    {
        try {
            $kelas = $request->input('kelas');
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

            if (!$kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak boleh kosong!'
                ], 400);
            }

            // Get all students in the class
            $siswaList = Siswa::where('kelas', $kelas)
                ->orderBy('nama_lengkap', 'asc')
                ->get();

            $evaluasiData = [];
            $countMelanjutkan = 0;
            $countMengulangi = 0;

            foreach ($siswaList as $siswa) {
                $perkembangan = Perkembangan::where('siswa_id', $siswa->id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                $kemampuan = 'mengulangi';
                $tilawati = '-';

                if ($perkembangan) {
                    $tilawati = $perkembangan->tilawati;
                    
                    if ($perkembangan->halaman) {
                        $tilawati .= ' hal. ' . $perkembangan->halaman;
                    }

                    if (in_array($perkembangan->kemampuan, ['Sangat Baik', 'Baik'])) {
                        $kemampuan = 'melanjutkan';
                    }
                }

                if ($kemampuan === 'melanjutkan') {
                    $countMelanjutkan++;
                } else {
                    $countMengulangi++;
                }

                $evaluasiData[] = [
                    'id' => $siswa->id,
                    'student_id' => $siswa->nis,
                    'student_name' => $siswa->nama_lengkap,
                    'tilawati' => $tilawati,
                    'kemampuan' => $kemampuan,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $evaluasiData,
                'stats' => [
                    'total' => count($evaluasiData),
                    'melanjutkan' => $countMelanjutkan,
                    'mengulangi' => $countMengulangi,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}