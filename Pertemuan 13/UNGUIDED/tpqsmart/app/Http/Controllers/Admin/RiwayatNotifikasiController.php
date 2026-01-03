<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatNotifikasiController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display riwayat notifikasi page
     */
    public function index()
    {
        try {
            // Get all notifications, latest first
            $notifikasiList = Notifikasi::with(['siswa', 'guru', 'sender'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notif) {
                    return (object)[
                        'id' => $notif->id,
                        'tanggal' => $notif->created_at,
                        'penerima' => $notif->nama_penerima,
                        'status' => $notif->status,
                        'pesan' => $notif->pesan,
                        'tipe' => $notif->tipe_notifikasi,
                    ];
                });

            // Get statistics
            $stats = Notifikasi::getStatistics();
            
            $totalBerhasil = $stats['berhasil'];
            $totalGagal = $stats['gagal'];
            $totalNotifikasi = $stats['total'];
            $hariIni = $stats['hari_ini'];

            return view('admin.riwayat_notifikasi', compact(
                'notifikasiList',
                'totalBerhasil',
                'totalGagal',
                'totalNotifikasi',
                'hariIni'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in riwayat notifikasi:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.dashboard_admin')
                ->with('error', 'Terjadi kesalahan saat memuat data notifikasi!');
        }
    }

    /**
     * Kirim notifikasi manual (AJAX)
     */
    public function send(Request $request)
    {
        try {
            $request->validate([
                'penerima' => 'required',
                'pesan' => 'required|max:1000',
            ]);

            $penerima = $request->penerima;
            $pesan = $request->pesan;
            
            $successCount = 0;
            $failCount = 0;
            $notifications = [];

            // Tentukan target penerima
            if ($penerima === 'Semua') {
                // Kirim ke semua orang tua
                $siswaList = Siswa::whereNotNull('no_hp')->get();
                
                foreach ($siswaList as $siswa) {
                    $result = $this->sendToSiswa($siswa, $pesan, 'manual');
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                    
                    $notifications[] = $result['notification'];
                }

            } elseif (in_array($penerima, ['Kelas A', 'Kelas B', 'Kelas C'])) {
                // Kirim ke kelas tertentu
                $siswaList = Siswa::where('kelas', $penerima)
                    ->whereNotNull('no_hp')
                    ->get();
                
                foreach ($siswaList as $siswa) {
                    $result = $this->sendToSiswa($siswa, $pesan, 'manual');
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                    
                    $notifications[] = $result['notification'];
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerima tidak valid!',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengirim {$successCount} pesan, {$failCount} gagal",
                'data' => [
                    'total' => $successCount + $failCount,
                    'success' => $successCount,
                    'failed' => $failCount,
                    'notifications' => $notifications,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending notification:', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim ulang notifikasi yang gagal (AJAX)
     */
    public function resend(Request $request)
    {
        try {
            $notifikasiId = $request->input('id');
            
            $notifikasi = Notifikasi::findOrFail($notifikasiId);
            
            if ($notifikasi->status === 'berhasil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi sudah berhasil dikirim sebelumnya',
                ], 400);
            }

            // Kirim ulang
            $result = $this->whatsappService->sendMessage(
                $notifikasi->no_hp,
                $notifikasi->pesan
            );

            if ($result['success']) {
                $notifikasi->markAsBerhasil();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil dikirim ulang!',
                ]);
            } else {
                $notifikasi->markAsGagal($result['message']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim ulang: ' . $result['message'],
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim ulang semua notifikasi yang gagal (AJAX)
     */
    public function resendAll(Request $request)
    {
        try {
            $gagalList = Notifikasi::gagal()->get();
            
            if ($gagalList->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada notifikasi yang gagal',
                ], 400);
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($gagalList as $notifikasi) {
                $result = $this->whatsappService->sendMessage(
                    $notifikasi->no_hp,
                    $notifikasi->pesan
                );

                if ($result['success']) {
                    $notifikasi->markAsBerhasil();
                    $successCount++;
                } else {
                    $notifikasi->markAsGagal($result['message']);
                    $failCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengirim ulang {$successCount} pesan, {$failCount} gagal",
                'data' => [
                    'success' => $successCount,
                    'failed' => $failCount,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: Send notification to siswa
     */
    protected function sendToSiswa($siswa, $pesan, $tipeNotifikasi = 'manual')
    {
        // Format nomor HP
        $nomorHP = Notifikasi::formatPhoneNumber($siswa->no_hp);

        // Create notification record
        $notifikasi = Notifikasi::create([
            'no_hp' => $nomorHP,
            'nama_penerima' => $siswa->nama_lengkap,
            'tipe_penerima' => 'orang_tua',
            'siswa_id' => $siswa->id,
            'tipe_notifikasi' => $tipeNotifikasi,
            'pesan' => $pesan,
            'status' => 'pending',
            'sent_by' => Auth::id(),
        ]);

        // Send WhatsApp
        $result = $this->whatsappService->sendMessage($nomorHP, $pesan);

        // Update status
        if ($result['success']) {
            $notifikasi->markAsBerhasil();
        } else {
            $notifikasi->markAsGagal($result['message']);
        }

        return [
            'success' => $result['success'],
            'notification' => $notifikasi,
        ];
    }

    /**
     * Auto-send notification setelah presensi
     */
    public static function sendPresensiNotification($presensi)
    {
        try {
            $siswa = $presensi->siswa;
            
            if (!$siswa || !$siswa->no_hp) {
                \Log::warning('Siswa tidak punya nomor HP:', ['siswa_id' => $presensi->siswa_id]);
                return false;
            }

            $whatsappService = new WhatsAppService();
            $pesan = WhatsAppService::templatePresensi($siswa, $presensi);
            $nomorHP = Notifikasi::formatPhoneNumber($siswa->no_hp);

            // Create notification record
            $notifikasi = Notifikasi::create([
                'no_hp' => $nomorHP,
                'nama_penerima' => $siswa->nama_lengkap,
                'tipe_penerima' => 'orang_tua',
                'siswa_id' => $siswa->id,
                'tipe_notifikasi' => 'presensi',
                'pesan' => $pesan,
                'status' => 'pending',
                'reference_type' => 'App\Models\Presensi',
                'reference_id' => $presensi->id,
            ]);

            // Send WhatsApp
            $result = $whatsappService->sendMessage($nomorHP, $pesan);

            RiwayatNotifikasiController::sendPresensiNotification($presensi);

            // Update status
            if ($result['success']) {
                $notifikasi->markAsBerhasil();
                return true;
            } else {
                $notifikasi->markAsGagal($result['message']);
                return false;
            }

        } catch (\Exception $e) {
            \Log::error('Error sending presensi notification:', [
                'error' => $e->getMessage(),
                'presensi_id' => $presensi->id,
            ]);
            return false;
        }
    }

    /**
     * Auto-send notification setelah perkembangan
     */
    public static function sendPerkembanganNotification($perkembangan)
    {
        try {
            $siswa = $perkembangan->siswa;
            
            if (!$siswa || !$siswa->no_hp) {
                \Log::warning('Siswa tidak punya nomor HP:', ['siswa_id' => $perkembangan->siswa_id]);
                return false;
            }

            $whatsappService = new WhatsAppService();
            $pesan = WhatsAppService::templatePerkembangan($siswa, $perkembangan);
            $nomorHP = Notifikasi::formatPhoneNumber($siswa->no_hp);

            // Create notification record
            $notifikasi = Notifikasi::create([
                'no_hp' => $nomorHP,
                'nama_penerima' => $siswa->nama_lengkap,
                'tipe_penerima' => 'orang_tua',
                'siswa_id' => $siswa->id,
                'tipe_notifikasi' => 'perkembangan',
                'pesan' => $pesan,
                'status' => 'pending',
                'reference_type' => 'App\Models\Perkembangan',
                'reference_id' => $perkembangan->id,
            ]);

            // Send WhatsApp
            $result = $whatsappService->sendMessage($nomorHP, $pesan);

           RiwayatNotifikasiController::sendPerkembanganNotification($perkembangan);

            // Update status
            if ($result['success']) {
                $notifikasi->markAsBerhasil();
                return true;
            } else {
                $notifikasi->markAsGagal($result['message']);
                return false;
            }

        } catch (\Exception $e) {
            \Log::error('Error sending perkembangan notification:', [
                'error' => $e->getMessage(),
                'perkembangan_id' => $perkembangan->id,
            ]);
            return false;
        }
    }
}