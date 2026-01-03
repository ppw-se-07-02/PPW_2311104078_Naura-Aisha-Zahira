<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Perkembangan extends Model
{
    use HasFactory;

    protected $table = 'perkembangans';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'tilawati',
        'halaman',
        'kemampuan',
        'hafalan',
        'ayat',
        'tata_krama',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
     * Accessor untuk format tanggal Indonesia
     */
    public function getTanggalIndonesiaAttribute()
    {
        return Carbon::parse($this->tanggal)->isoFormat('D MMMM YYYY');
    }

    /**
     * Helper: Cek apakah sudah ada perkembangan untuk siswa pada tanggal tertentu
     */
    public static function isRecordedOn($siswaId, $tanggal)
    {
        return self::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->exists();
    }

    /**
     * Helper: Get perkembangan terbaru untuk siswa
     */
    public static function getLatestForStudent($siswaId)
    {
        return self::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'desc')
            ->first();
    }

    /**
     * Helper: Count perkembangan yang sudah dicatat untuk kelas pada tanggal tertentu
     */
    public static function getCountByDate($tanggal, $kelas = null)
    {
        $query = self::byTanggal($tanggal);
        
        if ($kelas) {
            $query->byKelas($kelas);
        }

        return $query->count();
    }
}