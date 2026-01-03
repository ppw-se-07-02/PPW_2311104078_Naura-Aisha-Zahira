<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        // Ambil dari .env
        $this->apiUrl = env('FONNTE_API_URL', 'https://api.fonnte.com/send');
        $this->apiToken = env('FONNTE_API_TOKEN');
    }

    /**
     * Kirim pesan WhatsApp
     * 
     * @param string $phoneNumber - Format: 628123456789
     * @param string $message - Isi pesan
     * @return array
     */
    public function sendMessage($phoneNumber, $message)
    {
        try {
            // Validasi
            if (empty($this->apiToken)) {
                throw new \Exception('WhatsApp API Token tidak ditemukan! Set FONNTE_API_TOKEN di .env');
            }

            if (empty($phoneNumber)) {
                throw new \Exception('Nomor HP tidak boleh kosong');
            }

            if (empty($message)) {
                throw new \Exception('Pesan tidak boleh kosong');
            }

            // Format nomor HP
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Kirim request ke Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->apiUrl, [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ]);

            // Log response untuk debugging
            Log::info('WhatsApp API Response:', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            // Cek response
            if ($response->successful()) {
                $data = $response->json();
                
                // Fonnte return status: true jika berhasil
                if (isset($data['status']) && $data['status'] === true) {
                    return [
                        'success' => true,
                        'message' => 'Pesan berhasil dikirim',
                        'data' => $data,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['reason'] ?? 'Gagal mengirim pesan',
                        'data' => $data,
                    ];
                }
            } else {
                throw new \Exception('HTTP Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error:', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Kirim ke multiple nomor (broadcast)
     * 
     * @param array $phoneNumbers - Array nomor HP
     * @param string $message - Isi pesan
     * @return array
     */
    public function sendBroadcast(array $phoneNumbers, $message)
    {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($phoneNumbers as $phone) {
            $result = $this->sendMessage($phone, $message);
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            $results[] = [
                'phone' => $phone,
                'result' => $result,
            ];

            // Delay untuk avoid rate limit (optional)
            usleep(500000); // 0.5 detik
        }

        return [
            'total' => count($phoneNumbers),
            'success' => $successCount,
            'failed' => $failCount,
            'results' => $results,
        ];
    }

    /**
     * Format nomor HP untuk WhatsApp
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Replace leading 0 with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Add 62 if not exists
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Template pesan presensi
     */
    public static function templatePresensi($siswa, $presensi)
    {
        $status = $presensi->status == 'hadir' ? '✅ HADIR' : '❌ TIDAK HADIR';
        $tanggal = \Carbon\Carbon::parse($presensi->tanggal)->isoFormat('dddd, D MMMM YYYY');
        
        return "*🔔 NOTIFIKASI PRESENSI TPQ*\n\n"
             . "Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
             . "Yth. Orang Tua/Wali dari:\n"
             . "*{$siswa->nama_lengkap}*\n"
             . "NIS: {$siswa->nis}\n"
             . "Kelas: {$siswa->kelas}\n\n"
             . "━━━━━━━━━━━━━━━━━\n"
             . "📅 Tanggal: {$tanggal}\n"
             . "📍 Status: {$status}\n"
             . ($presensi->keterangan ? "📝 Keterangan: {$presensi->keterangan}\n" : "")
             . "━━━━━━━━━━━━━━━━━\n\n"
             . "Terima kasih atas perhatiannya.\n"
             . "Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
             . "_TPQ Smart - Sistem Informasi TPQ_";
    }

    /**
     * Template pesan perkembangan
     */
    public static function templatePerkembangan($siswa, $perkembangan)
    {
        $tanggal = \Carbon\Carbon::parse($perkembangan->tanggal)->isoFormat('dddd, D MMMM YYYY');
        
        return "*📊 LAPORAN PERKEMBANGAN SISWA*\n\n"
             . "Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
             . "Yth. Orang Tua/Wali dari:\n"
             . "*{$siswa->nama_lengkap}*\n"
             . "NIS: {$siswa->nis}\n"
             . "Kelas: {$siswa->kelas}\n\n"
             . "━━━━━━━━━━━━━━━━━\n"
             . "📅 Tanggal: {$tanggal}\n\n"
             . "📖 *Tilawati:* {$perkembangan->tilawati}" 
             . ($perkembangan->halaman ? " hal. {$perkembangan->halaman}" : "") . "\n"
             . "⭐ *Kemampuan:* {$perkembangan->kemampuan}\n"
             . "📿 *Hafalan:* {$perkembangan->hafalan}"
             . ($perkembangan->ayat ? " {$perkembangan->ayat}" : "") . "\n"
             . ($perkembangan->tata_krama ? "🤲 *Tata Krama:* {$perkembangan->tata_krama}\n" : "")
             . ($perkembangan->catatan ? "📝 *Catatan:* {$perkembangan->catatan}\n" : "")
             . "━━━━━━━━━━━━━━━━━\n\n"
             . "Semoga menjadi motivasi untuk terus belajar.\n"
             . "Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
             . "_TPQ Smart - Sistem Informasi TPQ_";
    }
}