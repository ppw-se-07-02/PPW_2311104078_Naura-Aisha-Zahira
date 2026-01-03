<?php 
// File: tes_koneksi.php 
include "koneksi.php"; 
 
if ($conn) { 
    echo "   Koneksi ke database berhasil!"; 
} else { 
    echo "  Koneksi ke database gagal!"; 
} 
?> 