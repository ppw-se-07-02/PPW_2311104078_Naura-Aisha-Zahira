<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin
     */
    public function index(Request $request)
    {
        // STEP 1: Hitung statistik utama
        $stats = $this->getStatistics();
        
        // STEP 2: Ambil data chart kehadiran
        $chartData = $this->getAttendanceChartData();
        
        // ✅ TAMBAHAN INI DOANG
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'chart' => $chartData
                ]
            ]);
        }


        return view('admin.dashboard_admin', [
            // Statistik Cards
            'totalGuru' => $stats['totalGuru'],
            'totalWali' => $stats['totalWali'],
            'totalSiswa' => $stats['totalSiswa'],
            'totalNotifikasi' => $stats['totalNotifikasi'],
            
            // Data Chart
            'chartLabels' => $chartData['labels'],
            'chartDatasets' => $chartData['datasets'],
            'kelasNames' => $chartData['kelasNames'],
            
            // Additional Stats
            'percentageGuru' => $stats['percentageGuru'],
            'percentageWali' => $stats['percentageWali'],
            'percentageSiswa' => $stats['percentageSiswa'],
        ]);
    }
    
    /**
     * Hitung statistik untuk card
     */
    private function getStatistics()
    {
        // ✅ Total Guru (dari tabel gurus)
        $totalGuru = Guru::count();
        
        // ✅ Total Siswa (dari tabel siswas)
        $totalSiswa = Siswa::count();
        
        // ✅ Total Orang Tua/Wali (berdasarkan no_hp yang UNIQUE)
        // Logika: Jika 2 siswa punya no_hp sama = 1 orang tua
        $totalWali = Siswa::distinct('no_hp')->count('no_hp');
        
        // ✅ Total Notifikasi (placeholder - nanti buat tabel notifications)
        $totalNotifikasi = 0; // TODO: Notifications::count();
        
        // Hitung persentase (untuk visual progress)
        $totalUsers = $totalGuru + $totalSiswa + $totalWali;
        $percentageGuru = $totalUsers > 0 ? round(($totalGuru / $totalUsers) * 100) : 0;
        $percentageWali = $totalUsers > 0 ? round(($totalWali / $totalUsers) * 100) : 0;
        $percentageSiswa = $totalUsers > 0 ? round(($totalSiswa / $totalUsers) * 100) : 0;
        
        return [
            'totalGuru' => $totalGuru,
            'totalWali' => $totalWali,
            'totalSiswa' => $totalSiswa,
            'totalNotifikasi' => $totalNotifikasi,
            'percentageGuru' => $percentageGuru,
            'percentageWali' => $percentageWali,
            'percentageSiswa' => $percentageSiswa,
        ];
    }
    
    /**
     * Ambil data untuk chart kehadiran per kelas
     */
    private function getAttendanceChartData()
    {
        // ✅ Ambil distribusi siswa per kelas
        $siswaPerKelas = Siswa::select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();
        
        // Jika tidak ada data siswa, return empty
        if ($siswaPerKelas->isEmpty()) {
            return [
                'labels' => ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
                'kelasNames' => [],
                'datasets' => [],
            ];
        }
        
        // Generate labels bulan
        $labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        
        // Transform data untuk Chart.js
        $datasets = [];
        $kelasNames = [];
        
        foreach ($siswaPerKelas as $kelas) {
            $kelasNames[] = 'Kelas ' . $kelas->kelas;
            
            // Generate dummy data untuk demo (nanti diganti dengan data presensi real)
            $data = [];
            for ($i = 0; $i < 12; $i++) {
                // Random antara 0 - total siswa di kelas
                $data[] = rand(0, $kelas->total);
            }
            
            $datasets[] = [
                'label' => 'Kelas ' . $kelas->kelas,
                'data' => $data
            ];
        }
        
        return [
            'labels' => $labels,
            'kelasNames' => $kelasNames,
            'datasets' => $datasets,
        ];
    }
    
    /**
     * API untuk filter chart (AJAX)
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', '12');
        
        $labels = $this->getLabelsForPeriod($period);
        $chartData = $this->getAttendanceChartData();
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'datasets' => $chartData['datasets'],
        ]);
    }
    
    /**
     * Helper untuk generate label chart sesuai periode
     */
    private function getLabelsForPeriod($months)
    {
        if ($months == 1) {
            return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        } elseif ($months == 6) {
            return ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'];
        } else {
            return ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        }
    }
}