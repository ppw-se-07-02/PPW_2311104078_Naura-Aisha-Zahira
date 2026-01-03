<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * Display presensi page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $selectedClass = $request->input('class', null);

        // Get list of classes (distinct from siswas)
        $classList = Siswa::select('kelas', DB::raw('count(*) as students_count'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get()
            ->map(function($item) {
                return (object) [
                    'id' => $item->kelas,
                    'nama' => 'Kelas ' . $item->kelas,
                    'students_count' => $item->students_count
                ];
            });

        // Default to first class if not selected
        if (!$selectedClass && $classList->isNotEmpty()) {
            $selectedClass = $classList->first()->id;
        }

        // Get presensi data
        $presensiData = $this->getPresensiData($selectedDate, $selectedClass);
        
        // Total students in selected class
        $totalStudents = Siswa::where('kelas', $selectedClass)->count();
        
        // ✅ TAMBAHKAN INI - Return JSON untuk API request
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'selectedDate' => $selectedDate,
                    'selectedClass' => $selectedClass,
                    'classList' => $classList,
                    'presensiData' => $presensiData,
                    'totalStudents' => $totalStudents,
                ]
            ]);
        }

        return view('admin.data_presensi', [
            'selectedDate' => $selectedDate,
            'selectedClass' => $selectedClass ? 'Kelas ' . $selectedClass : 'Pilih Kelas',
            'classList' => $classList,
            'presensiData' => $presensiData,
            'totalStudents' => $totalStudents,
        ]);
    }

    /**
     * Get presensi data for specific date and class
     */
    private function getPresensiData($tanggal, $kelas)
    {
        if (!$kelas) {
            return [];
        }

        // Get all students in the class
        $students = Siswa::where('kelas', $kelas)->with('user')->get();

        if ($students->isEmpty()) {
            return [];
        }

        $presensiData = [];

        foreach ($students as $student) {
            // Check if presensi exists for this student on this date
            $presensi = Presensi::where('siswa_id', $student->id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            // If no presensi exists, create default (alpha)
            if (!$presensi) {
                $presensi = new Presensi([
                    'siswa_id' => $student->id,
                    'tanggal' => $tanggal,
                    'status' => 'alpha',
                    'waktu' => null,
                ]);
                // Don't save yet, just for display
            }

            $presensiData[] = (object) [
                'id' => $presensi->id ?? 'new_' . $student->id,
                'siswa_id' => $student->id,
                'student_id' => $student->nis,
                'student_name' => $student->nama_lengkap,
                'status' => $presensi->status,
                'waktu' => $presensi->waktu ? Carbon::parse($presensi->waktu)->format('H:i') : '-',
                'keterangan' => $presensi->keterangan ?? '',
            ];
        }

        return $presensiData;
    }

    /**
     * Update single presensi
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Set waktu otomatis jika status = hadir
            $waktu = null;
            if ($validated['status'] === 'hadir') {
                $waktu = Carbon::now()->format('H:i:s');
            }

            // Update or create presensi
            $presensi = Presensi::updateOrCreate(
                [
                    'siswa_id' => $validated['siswa_id'],
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'status' => $validated['status'],
                    'waktu' => $waktu,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'dicatat_oleh' => auth()->user()->name ?? 'System',
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil diperbarui!',
                'data' => [
                    'id' => $presensi->id,
                    'status' => $presensi->status,
                    'waktu' => $presensi->waktu ? Carbon::parse($presensi->waktu)->format('H:i') : '-',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error updating presensi:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update presensi (untuk reset atau set semua)
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kelas' => 'required|string',
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        try {
            DB::beginTransaction();

            // Get all students in the class
            $students = Siswa::where('kelas', $validated['kelas'])->get();

            $waktu = $validated['status'] === 'hadir' ? Carbon::now()->format('H:i:s') : null;

            foreach ($students as $student) {
                Presensi::updateOrCreate(
                    [
                        'siswa_id' => $student->id,
                        'tanggal' => $validated['tanggal'],
                    ],
                    [
                        'status' => $validated['status'],
                        'waktu' => $waktu,
                        'dicatat_oleh' => auth()->user()->name ?? 'System',
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil diperbarui untuk semua siswa!',
                'count' => $students->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error bulk updating presensi:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset presensi (set all to alpha)
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kelas' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Get all students in the class
            $students = Siswa::where('kelas', $validated['kelas'])->pluck('id');

            // Delete existing presensi or set to alpha
            Presensi::where('tanggal', $validated['tanggal'])
                ->whereIn('siswa_id', $students)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil direset!',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error resetting presensi:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export presensi to CSV
     */
    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kelas = $request->input('kelas');

        $presensiData = $this->getPresensiData($tanggal, $kelas);

        // Generate CSV
        $filename = 'Presensi_Kelas_' . $kelas . '_' . $tanggal . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($presensiData) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['NIS', 'Nama', 'Status', 'Waktu', 'Keterangan']);

            // Data
            foreach ($presensiData as $row) {
                fputcsv($file, [
                    $row->student_id,
                    $row->student_name,
                    ucfirst($row->status),
                    $row->waktu,
                    $row->keterangan ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kelas = $request->input('kelas');

        $stats = Presensi::getCountByStatus($tanggal, $kelas);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}