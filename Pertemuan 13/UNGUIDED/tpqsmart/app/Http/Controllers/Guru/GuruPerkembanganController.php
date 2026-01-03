<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Perkembangan;
use Carbon\Carbon;

class GuruPerkembanganController extends Controller
{
    /**
     * Display perkembangan page
     */
    public function index(Request $request)
    {
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

        // Ambil data perkembangan untuk tanggal ini
        $perkembanganData = [];
        foreach ($siswaList as $siswa) {
            $perkembangan = Perkembangan::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $selectedDate)
                ->first();

            $perkembanganData[] = [
                'siswa_id' => $siswa->id,
                'id' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'status' => $perkembangan ? 'sudah' : 'belum',
                'tilawati' => $perkembangan ? $perkembangan->tilawati : null,
                'halaman' => $perkembangan ? $perkembangan->halaman : null,
                'kemampuan' => $perkembangan ? $perkembangan->kemampuan : null,
                'hafalan' => $perkembangan ? $perkembangan->hafalan : null,
                'ayat' => $perkembangan ? $perkembangan->ayat : null,
                'tata_krama' => $perkembangan ? $perkembangan->tata_krama : null,
                'catatan' => $perkembangan ? $perkembangan->catatan : null,
            ];
        }

        return view('guru.perkembangan', [
            'guru' => $guru,
            'siswaList' => $siswaList,
            'perkembanganData' => $perkembanganData,
            'selectedDate' => $selectedDate,
            'totalSiswa' => $siswaList->count(),
            'sudahDicatat' => count(array_filter($perkembanganData, fn($p) => $p['status'] === 'sudah')),
            'belumDicatat' => count(array_filter($perkembanganData, fn($p) => $p['status'] === 'belum')),
        ]);
    }

    /**
     * Store perkembangan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'tilawati' => 'required|string',
            'halaman' => 'nullable|string|max:50',
            'kemampuan' => 'required|in:Sangat Baik,Baik,Cukup,Perlu Bimbingan',
            'hafalan' => 'required|string|max:255',
            'ayat' => 'nullable|string|max:50',
            'tata_krama' => 'nullable|string',
            'catatan' => 'nullable|string',
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

            // Update or create
            $perkembangan = Perkembangan::updateOrCreate(
                [
                    'siswa_id' => $validated['siswa_id'],
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'tilawati' => $validated['tilawati'],
                    'halaman' => $validated['halaman'] ?? null,
                    'kemampuan' => $validated['kemampuan'],
                    'hafalan' => $validated['hafalan'],
                    'ayat' => $validated['ayat'] ?? null,
                    'tata_krama' => $validated['tata_krama'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                    'dicatat_oleh' => $guru->nama_lengkap,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perkembangan berhasil disimpan!',
                'data' => [
                    'id' => $perkembangan->id,
                    'siswa_id' => $perkembangan->siswa_id,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving perkembangan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan perkembangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get perkembangan by date (AJAX)
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

        $perkembanganData = [];
        foreach ($siswaList as $siswa) {
            $perkembangan = Perkembangan::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $perkembanganData[] = [
                'siswa_id' => $siswa->id,
                'id' => $siswa->nis,
                'nama' => $siswa->nama_lengkap,
                'status' => $perkembangan ? 'sudah' : 'belum',
                'tilawati' => $perkembangan ? $perkembangan->tilawati : null,
                'kemampuan' => $perkembangan ? $perkembangan->kemampuan : null,
                'hafalan' => $perkembangan ? $perkembangan->hafalan : null,
                'tata_krama' => $perkembangan ? $perkembangan->tata_krama : null,
                'catatan' => $perkembangan ? $perkembangan->catatan : null,
            ];
        }

        $sudahDicatat = count(array_filter($perkembanganData, fn($p) => $p['status'] === 'sudah'));
        $belumDicatat = count(array_filter($perkembanganData, fn($p) => $p['status'] === 'belum'));

        return response()->json([
            'success' => true,
            'data' => $perkembanganData,
            'stats' => [
                'total' => count($perkembanganData),
                'sudah' => $sudahDicatat,
                'belum' => $belumDicatat,
            ]
        ]);
    }

    /**
     * Get detail perkembangan siswa (AJAX)
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

        // Pastikan siswa ada di kelas guru
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas', $guru->kelas)
            ->first();

        if (!$siswa) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        $perkembangan = Perkembangan::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'nama' => $siswa->nama_lengkap,
                ],
                'perkembangan' => $perkembangan ? [
                    'tilawati' => $perkembangan->tilawati,
                    'halaman' => $perkembangan->halaman,
                    'kemampuan' => $perkembangan->kemampuan,
                    'hafalan' => $perkembangan->hafalan,
                    'ayat' => $perkembangan->ayat,
                    'tata_krama' => $perkembangan->tata_krama,
                    'catatan' => $perkembangan->catatan,
                ] : null
            ]
        ]);
    }
}