<?php 
// File: proses_tambah.php 
// Fungsi: Memproses data dari form dan menyimpan ke database 
 
include "koneksi.php"; 
 
// Cek apakah form sudah di-submit 
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
     
    // Ambil data dari form 
    $nim = $_POST['nim']; 
    $nama = $_POST['nama']; 
    $jurusan = $_POST['jurusan']; 
    $email = $_POST['email']; 
    $tanggal_lahir = $_POST['tanggal_lahir']; 
     
    // Validasi: Cek apakah NIM sudah ada 
    $cek_nim = mysqli_query($conn, "SELECT nim FROM mahasiswa WHERE nim='$nim'"); 
     
    if (mysqli_num_rows($cek_nim) > 0) { 
        // NIM sudah ada 
        echo "<script> 
                alert('  NIM sudah terdaftar! Gunakan NIM lain.'); 
                window.location.href='form_tambah.php'; 
              </script>"; 
    } else { 
        // Query INSERT untuk menambah data 
        $query = "INSERT INTO mahasiswa (nim, nama, jurusan, email, tanggal_lahir)  
                  VALUES ('$nim', '$nama', '$jurusan', '$email', '$tanggal_lahir')"; 
         
        // Eksekusi query 
        if (mysqli_query($conn, $query)) { 
            echo "<script> 
                    alert('   Data berhasil ditambahkan!'); 
                    window.location.href='tampil_data.php'; 
                  </script>"; 
        } else { 
            echo "<script> 
                    alert('  Gagal menambahkan data: " . mysqli_error($conn) . "'); 
                    window.location.href='form_tambah.php'; 
                  </script>"; 
        } 
    } 
} 
 
// Tutup koneksi 
mysqli_close($conn); 
?>