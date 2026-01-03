<?php
include "koneksi.php";

// 1. Aksi Hapus
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = intval($_GET['id']);
    $query = "DELETE FROM produk WHERE id_produk=$id";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='kelola_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus!'); window.location.href='kelola_produk.php';</script>";
    }
}

// 2. Aksi Tambah
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    $query = "INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil ditambah!'); window.location.href='kelola_produk.php';</script>";
    }
}

// 3. Aksi Edit
if (isset($_POST['edit'])) {
    $id = intval($_POST['id_produk']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    $query = "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok' WHERE id_produk=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='kelola_produk.php';</script>";
    }
}

mysqli_close($conn);
?>