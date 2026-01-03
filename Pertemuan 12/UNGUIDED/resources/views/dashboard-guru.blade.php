<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - TPQSmart</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <!-- Menampilkan gambar dari folder img -->
            <img src="{{ asset('assets/img/Guru.png') }}" alt="Guru TPQ Baitul Muttaqin">
            <h1>Hi, Bu Chae</h1>
            <p>Guru</p>
        </header>

        <div class="menu-grid">
            <!-- Menu 1: Presensi -->
            <div class="menu-item">
                <img src="{{ asset('assets/img/presensi.png') }}" alt="Icon Presensi">
                <h3>Presensi</h3>
            </div>

            <!-- Menu 2: Profil Siswa -->
            <div class="menu-item">
                <img src="{{ asset('assets/img/profile.png') }}" alt="Icon Profil">
                <h3>Profil Siswa</h3>
            </div>

            <!-- Menu 3: Perkembangan -->
            <div class="menu-item">
                <img src="{{ asset('assets/img/perkembangan.png') }}" alt="Icon Perkembangan">
                <h3>Perkembangan</h3>
            </div>

            <!-- Menu 4: Rekap Evaluasi -->
            <div class="menu-item">
                <img src="{{ asset('assets/img/laporan.png') }}" alt="Icon Laporan">
                <h3>Rekap Evaluasi</h3>
            </div>
        </div>

        <div class="logout-container">
            <button class="btn-logout" onclick="handleLogout()">Keluar</button>
        </div>
    </div>

    <!-- Mengakses JavaScript dari folder js -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>