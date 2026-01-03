<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'status',
        'waktu',
        'keterangan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i',
    ];

    /**
     * Relasi ke Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Scope untuk filter by tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter by kelas (melalui siswa)
     */
    public function scopeByKelas($query, $kelas)
    {
        return $query->whereHas('siswa', function($q) use ($kelas) {
            $q->where('kelas', $kelas);
        });
    }

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getTanggalIndonesiaAttribute()
    {
        return Carbon::parse($this->tanggal)->isoFormat('D MMMM YYYY');
    }

    /**
     * Accessor untuk format waktu
     */
    public function getWaktuFormatAttribute()
    {
        return $this->waktu ? Carbon::parse($this->waktu)->format('H:i') : '-';
    }

    /**
     * Helper: Get count by status untuk tanggal tertentu
     */
    public static function getCountByStatus($tanggal, $kelas = null)
    {
        $query = self::byTanggal($tanggal);
        
        if ($kelas) {
            $query->byKelas($kelas);
        }

        return [
            'total' => $query->count(),
            'hadir' => $query->clone()->byStatus('hadir')->count(),
            'izin' => $query->clone()->byStatus('izin')->count(),
            'sakit' => $query->clone()->byStatus('sakit')->count(),
            'alpha' => $query->clone()->byStatus('alpha')->count(),
        ];
    }
}