<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mahasiswas extends Model {
    protected $fillable = [
        'nim', 
        'nama_lengkap', 
        'tempat_lahir', 
        'tanggal_lahir', 
        'fakultas', 
        'jurusan', 
        'alamat'
    ];
}

