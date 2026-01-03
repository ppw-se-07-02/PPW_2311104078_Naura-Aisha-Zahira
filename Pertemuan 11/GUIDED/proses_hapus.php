<?php
// File: proses_hapus.php
// Fungsi: Menghapus data mahasiswa dari database

include "koneksi.php";

// Ambil NIM dari URL
$nim = $_GET['nim'];

// Query DELETE untuk menghapus data
$query = "DELETE FROM mahasiswa WHERE nim='$nim'";

// Eksekusi query
if (mysqli_query($conn, $query)) {
    echo "<script>
            alert(' Data berhasil dihapus!');
            window.location.href='tampil_data.php';
          </script>";
} else {
    echo "<script>
            alert(' Gagal menghapus data: " . mysqli_error($conn) . "');
            window.location.href='tampil_data.php';
          </script>";
}

mysqli_close($conn);
?>