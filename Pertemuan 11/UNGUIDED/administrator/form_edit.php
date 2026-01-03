<?php
include "koneksi.php";

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: kelola_produk.php");
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM produk WHERE id_produk = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='kelola_produk.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Form Edit Produk</h4>
                    </div>
                    <div class="card-body">
                        <form action="proses_produk.php" method="POST">
                            <input type="hidden" name="id_produk" value="<?= $data['id_produk'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($data['nama_produk']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" required value="<?= $data['harga'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" required value="<?= $data['stok'] ?>">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="kelola_produk.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="edit" class="btn btn-warning">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>