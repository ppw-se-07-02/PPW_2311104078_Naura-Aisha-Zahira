<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Presensi;
use Carbon\Carbon;

class GuruPresensiController extends Controller
{
    /**
     * Display presensi page
     */
    public function index(Request $request)
    {
        // Ambil user yang login
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        // Tanggal yang dipilih (default hari ini)
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        // Ambil semua siswa di kelas guru
        $siswaList = Siswa::where('kelas', $guru->kelas)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Ambil data presensi untuk tanggal ini
        $presensiData = [];
        foreach ($siswaList as $siswa) {
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $selectedDate)
                ->first();

            $presensiData[] = [
                'siswa_id' => $siswa->id,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'status' => $presensi ? $presensi->status : null,
                'waktu' => $presensi && $presensi->waktu ? Carbon::parse($presensi->waktu)->format('H:i') : null,
                'keterangan' => $presensi ? $presensi->keterangan : null,
            ];
        }

        // Hitung statistik
        $stats = $this->getStatistics($selectedDate, $guru->kelas);

        return view('guru.presensi', [
            'guru' => $guru,
            'siswaList' => $siswaList,
            'presensiData' => $presensiData,
            'selectedDate' => $selectedDate,
            'stats' => $stats,
            'totalSiswa' => $siswaList->count()
        ]);
    }

    /**
     * Simpan presensi (batch save)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.siswa_id' => 'required|exists:siswas,id',
            'presensi.*.status' => 'required|in:hadir,izin,sakit,alpha',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $savedCount = 0;

            foreach ($validated['presensi'] as $data) {
                // Pastikan siswa ada di kelas guru
                $siswa = Siswa::where('id', $data['siswa_id'])
                    ->where('kelas', $guru->kelas)
                    ->first();

                if (!$siswa) {
                    continue; // Skip kalau bukan siswa di kelas guru
                }

                // Set waktu otomatis jika hadir
                $waktu = null;
                if ($data['status'] === 'hadir') {
                    $waktu = Carbon::now()->format('H:i:s');
                }

                // Update or create
                Presensi::updateOrCreate(
                    [
                        'siswa_id' => $data['siswa_id'],
                        'tanggal' => $validated['tanggal'],
                    ],
                    [
                        'status' => $data['status'],
                        'waktu' => $waktu,
                        'keterangan' => $data['keterangan'] ?? null,
                        'dicatat_oleh' => $guru->nama_lengkap,
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            // Hitung statistik baru
            $stats = $this->getStatistics($validated['tanggal'], $guru->kelas);

            return response()->json([
                'success' => true,
                'message' => "Presensi berhasil disimpan untuk {$savedCount} siswa!",
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving presensi:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update single presensi (untuk quick toggle)
     */
    public function updateSingle(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!'
            ], 403);
        }

        try {
            // Pastikan siswa ada di kelas guru
            $siswa = Siswa::where('id', $validated['siswa_id'])
                ->where('kelas', $guru->kelas)
                ->first();

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan di kelas Anda!'
                ], 404);
            }

            DB::beginTransaction();

            $waktu = null;
            if ($validated['status'] === 'hadir') {
                $waktu = Carbon::now()->format('H:i:s');
            }

            $presensi = Presensi::updateOrCreate(
                [
                    'siswa_id' => $validated['siswa_id'],
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'status' => $validated['status'],
                    'waktu' => $waktu,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'dicatat_oleh' => $guru->nama_lengkap,
                ]
            );

            DB::commit();

            // Hitung statistik baru
            $stats = $this->getStatistics($validated['tanggal'], $guru->kelas);

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil diperbarui!',
                'data' => [
                    'id' => $presensi->id,
                    'status' => $presensi->status,
                    'waktu' => $presensi->waktu ? Carbon::parse($presensi->waktu)->format('H:i') : null,
                ],
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error updating single presensi:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    private function getStatistics($tanggal, $kelas)
    {
        $totalSiswa = Siswa::where('kelas', $kelas)->count();

        $presensiCount = Presensi::whereDate('tanggal', $tanggal)
            ->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => $totalSiswa,
            'hadir' => $presensiCount['hadir'] ?? 0,
            'izin' => $presensiCount['izin'] ?? 0,
            'sakit' => $presensiCount['sakit'] ?? 0,
            'alpha' => $totalSiswa - array_sum($presensiCount),
        ];
    }

    /**
     * Get presensi by date (AJAX)
     */
    public function getByDate(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $siswaList = Siswa::where('kelas', $guru->kelas)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $presensiData = [];
        foreach ($siswaList as $siswa) {
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $presensiData[] = [
                'siswa_id' => $siswa->id,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'status' => $presensi ? $presensi->status : null,
                'waktu' => $presensi && $presensi->waktu ? Carbon::parse($presensi->waktu)->format('H:i') : null,
            ];
        }

        $stats = $this->getStatistics($tanggal, $guru->kelas);

        return response()->json([
            'success' => true,
            'data' => $presensiData,
            'stats' => $stats
        ]);
    }
}