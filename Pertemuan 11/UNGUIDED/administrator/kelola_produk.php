<?php 
include "koneksi.php"; 

// Logika Pencarian
$keyword = "";
if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $query = "SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' ORDER BY id_produk DESC";
} else {
    $query = "SELECT * FROM produk ORDER BY id_produk DESC";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Kelola Produk Toko</h3>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari nama produk..." value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-info text-white">Cari</button>
                        <a href="kelola_produk.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <a href="form_tambah.php" class="btn btn-success mb-3">+ Tambah Produk</a>

                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $row['id_produk'] ?></td>
                                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td>
                                        <a href="form_edit.php?id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="proses_produk.php?action=hapus&id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>