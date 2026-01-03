<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'gurus';

    // Mass assignment
    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'foto',
    ];

    // Cast types
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke User (akun login)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ TAMBAHAN: Relasi ke Siswa (siswa di kelas yang diajar)
     * Guru bisa ngajar banyak siswa
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas', 'kelas');
    }

     /**
     * ✅ TAMBAHAN: Hitung total siswa di kelas ini
     */
    public function getTotalSiswaAttribute()
    {
        return Siswa::where('kelas', $this->kelas)->count();
    }

    /**
     * ✅ TAMBAHAN: Hitung siswa hadir hari ini
     */
    public function getSiswaHadirHariIniAttribute()
    {
        // Nanti kalau udah ada tabel presensi
        return 0; // Placeholder dulu
    }

    /**
     * ✅ TAMBAHAN: Persentase kehadiran hari ini
     */
    public function getPersentaseKehadiranAttribute()
    {
        if ($this->total_siswa == 0) return 0;
        return round(($this->siswa_hadir_hari_ini / $this->total_siswa) * 100);
    }

    /**
     * Accessor untuk foto URL
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('assets/img/default-avatar.png');
    }

    /**
     * Accessor untuk umur (auto calculate)
     */
    public function getUmurAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_lahir)->age;
    }
}