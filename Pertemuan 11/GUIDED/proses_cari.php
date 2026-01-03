<?php
// File: proses_cari.php
// Fungsi: Logika pencarian data mahasiswa

function cariMahasiswa($conn, $keyword)
{
    // Escape keyword untuk keamanan
    $keyword = mysqli_real_escape_string($conn, $keyword);

    // Query dengan LIKE untuk pencarian
    $query = "SELECT * FROM mahasiswa
              WHERE nim LIKE '%$keyword%'
              OR nama LIKE '%$keyword%'
              OR jurusan LIKE '%$keyword%'
              ORDER BY nim ASC";

    $result = mysqli_query($conn, $query);

    return $result;
}

function hitungHasilCari($result)
{
    return mysqli_num_rows($result);
}
?>