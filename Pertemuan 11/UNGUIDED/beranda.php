<?php 
include "administrator/koneksi.php"; 

// Mengambil data produk dari database untuk ditampilkan di halaman depan
$query = "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 6";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online Popmart (Dinamis)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header id="header" class="text-center my-4">
            <h1>Popmart Online Store</h1>
        </header>

        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="beranda.php">Beranda</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#">Produk</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Member</a></li>
                        <li class="nav-item"><a class="nav-link" href="administrator/kelola_produk.php">Admin Panel</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <section id="banner" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/image/banner-popmart.png" class="d-block w-100" alt="Banner 1">
                </div>
            </div>
        </section>

        <div class="row">
            <main class="col-lg-9">
                <h3 class="mb-4">Koleksi Produk Terbaru</h3>
                <section id="produk" class="row">
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { 
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="assets/image/produk_default.jpg" class="card-img-top" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                                <p class="card-text text-danger fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                                <p class="card-text"><small class="text-muted">Stok tersedia: <?= $row['stok'] ?></small></p>
                                <button class="btn btn-primary btn-sm w-100">Beli Sekarang</button>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                        echo "<p class='text-center'>Belum ada produk yang terdaftar.</p>";
                    } 
                    ?>
                </section>
            </main>

            <aside id="rekomendasi" class="col-lg-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Rekomendasi</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="assets/image/popmart7.jpg" class="img-fluid rounded mb-2" alt="Rekomendasi">
                        <h6>Popmart Molly Steampunk</h6>
                    </div>
                </div>
            </aside>
        </div>

        <footer class="bg-dark text-white text-center p-4 mt-4 rounded">
            <p>© 2026 Popmart Online Store - Kelola data melalui Database MySQL</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>