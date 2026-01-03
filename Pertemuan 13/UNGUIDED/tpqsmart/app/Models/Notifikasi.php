<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_hp',
        'nama_penerima',
        'tipe_penerima',
        'siswa_id',
        'guru_id',
        'tipe_notifikasi',
        'pesan',
        'status',
        'error_message',
        'sent_at',
        'reference_type',
        'reference_id',
        'sent_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========
    
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // ========== SCOPES ==========
    
    public function scopeBerhasil($query)
    {
        return $query->where('status', 'berhasil');
    }

    public function scopeGagal($query)
    {
        return $query->where('status', 'gagal');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe_notifikasi', $tipe);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    // ========== ACCESSORS ==========
    
    public function getTanggalIndonesiaAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('D MMMM YYYY, HH:mm');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'berhasil' => '<span class="badge bg-success">Berhasil</span>',
            'gagal' => '<span class="badge bg-danger">Gagal</span>',
            'pending' => '<span class="badge bg-warning">Pending</span>',
        ];
        
        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // ========== HELPER METHODS ==========
    
    /**
     * Tandai notifikasi sebagai berhasil
     */
    public function markAsBerhasil()
    {
        $this->update([
            'status' => 'berhasil',
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Tandai notifikasi sebagai gagal
     */
    public function markAsGagal($errorMessage = null)
    {
        $this->update([
            'status' => 'gagal',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get statistics
     */
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'berhasil' => self::berhasil()->count(),
            'gagal' => self::gagal()->count(),
            'pending' => self::pending()->count(),
            'hari_ini' => self::hariIni()->count(),
        ];
    }

    /**
     * Format nomor HP untuk WhatsApp API
     */
    public static function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}