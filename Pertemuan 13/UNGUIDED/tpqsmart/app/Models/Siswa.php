<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'nis',
        'nama_lengkap',
        'kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'foto',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ TAMBAHAN: Relasi ke Presensi
     * Siswa punya banyak presensi
     */
    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * ✅ TAMBAHAN: Ambil presensi untuk tanggal tertentu
     */
    public function presensiOnDate($tanggal)
    {
        return $this->presensi()->whereDate('tanggal', $tanggal)->first();
    }

    /**
     * ✅ TAMBAHAN: Hitung total kehadiran
     */
    public function getTotalHadirAttribute()
    {
        return $this->presensi()->where('status', 'hadir')->count();
    }

    /**
     * ✅ TAMBAHAN: Persentase kehadiran
     */
    public function getPersentaseKehadiranAttribute()
    {
        $total = $this->presensi()->count();
        if ($total == 0) return 0;

        $hadir = $this->total_hadir;
        return round(($hadir / $total) * 100, 1);
    }

    public function perkembangans()
    {
    return $this->hasMany(Perkembangan::class, 'siswa_id', 'id');
    }
    
    // Accessor untuk foto (optional, tapi berguna)
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('assets/img/default-avatar.png');
    }
}