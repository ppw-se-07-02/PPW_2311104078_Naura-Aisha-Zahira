<?php
// File: proses_edit.php
// Fungsi: Memproses update data mahasiswa

include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $email = $_POST['email'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    // Query UPDATE untuk mengubah data
    $query = "UPDATE mahasiswa SET
                nama='$nama',
                jurusan='$jurusan',
                email='$email',
                tanggal_lahir='$tanggal_lahir'
                WHERE nim='$nim'";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert(' Data berhasil diupdate!');
                window.location.href='tampil_data.php';
              </script>";
    } else {
        echo "<script>
                alert(' Gagal mengupdate data: " . mysqli_error($conn) . "');
                window.location.href='form_edit.php?nim=$nim';
              </script>";
    }
}

mysqli_close($conn);
?>