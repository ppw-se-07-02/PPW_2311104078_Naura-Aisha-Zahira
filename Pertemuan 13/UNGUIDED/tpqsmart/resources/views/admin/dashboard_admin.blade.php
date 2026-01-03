<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TPQ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet"/>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-section">
            <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="TPQ Logo" class="sidebar-logo"/>
        </div>
        
        <nav class="nav-menu">
            <a href="{{ route('admin.dashboard_admin') }}" class="nav-item">
                <i class="bi bi-house-door-fill"></i>
                <span class="nav-text">Beranda</span>
            </a>
            <a href="{{ route('admin.data_pengguna') }}" class="nav-item">
                <i class="bi bi-people-fill"></i>
                <span class="nav-text">Data Pengguna</span>
            </a>
            <a href="{{ route('admin.data_presensi') }}" class="nav-item">
                <i class="bi bi-calendar-check-fill"></i>
                <span class="nav-text">Data Presensi</span>
            </a>
            <a href="{{ route('admin.laporan_evaluasi') }}" class="nav-item">
                <i class="bi bi-bar-chart-fill"></i>
                <span class="nav-text">Laporan Evaluasi</span>
            </a>
            <a href="{{ route('admin.riwayat_notifikasi') }}" class="nav-item">
                <i class="bi bi-bell-fill"></i>
                <span class="nav-text">Riwayat Notifikasi</span>
            </a>
            <a href="#" class="nav-item" id="btnLogout" title="Keluar">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-text">Keluar</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <button class="btn-menu" id="btnMenu">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name">{{ Auth::check() ? Auth::user()->name : 'Admin TPQ' }}</span>
                    <span class="user-role">{{ Auth::check() ? ucfirst(Auth::user()->role) : 'Administrator' }}</span>
                </div>
                <img src="{{ asset('assets/img/default-avatar.png') }}" alt="User Avatar" class="user-avatar">
            </div>
        </header>

        <div class="container-fluid px-4 py-4">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-content">
                    <h2 class="welcome-title">Haiii, {{ Auth::user()->name ?? 'Admin' }}! 👋</h2>
                    <p class="welcome-subtitle">Berikut adalah ringkasan sistem TPQ hari ini</p>
                    
                    <div class="welcome-stats">
                        <div class="welcome-stat-item">
                            <div class="welcome-stat-icon">
                                <i class="bi bi-person-video3"></i>
                            </div>
                            <div class="welcome-stat-info">
                                <h4>{{ $totalGuru }}</h4>
                                <p>Guru Aktif</p>
                            </div>
                        </div>
                        
                        <div class="welcome-stat-item">
                            <div class="welcome-stat-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="welcome-stat-info">
                                <h4>{{ $totalOrangTua }}</h4>
                                <p>Orang Tua</p>
                            </div>
                        </div>
                        
                        <div class="welcome-stat-item">
                            <div class="welcome-stat-icon">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div class="welcome-stat-info">
                                <h4>{{ $totalSiswa }}</h4>
                                <p>Siswa Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <!-- Card 1: Total Guru -->
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <span class="stat-label">Total Guru</span>
                            </div>
                            <div class="stat-icon bg-guru">
                                <i class="bi bi-person-video3"></i>
                            </div>
                        </div>
                        <div class="stat-value-container">
                            <h3 class="stat-value">{{ $totalGuru }}</h3>
                            <p class="stat-subtitle">Pengajar TPQ</p>
                        </div>
        
                        <div class="stat-trend {{ $totalGuru > 0 ? 'up' : 'neutral' }}">
                            <i class="bi bi-{{ $totalGuru > 0 ? 'graph-up' : 'dash-circle' }}"></i>
                            <span>{{ $totalGuru > 0 ? 'Data Terkini' : 'Belum Ada Data' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Wali -->
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <span class="stat-label">Total Orang Tua</span>
                            </div>
                            <div class="stat-icon bg-wali">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value-container">
                            <h3 class="stat-value">{{ $totalOrangTua }}</h3>
                            <p class="stat-subtitle">Orang Tua Siswa</p>
                        </div>
                        
                        <div class="stat-trend {{ $totalOrangTua > 0 ? 'up' : 'neutral' }}">
                            <i class="bi bi-{{ $totalOrangTua > 0 ? 'graph-up' : 'dash-circle' }}"></i>
                            <span>{{ $totalOrangTua > 0 ? 'Data Terkini' : 'Belum Ada Data' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Total Siswa -->
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <span class="stat-label">Total Siswa</span>
                            </div>
                            <div class="stat-icon bg-siswa">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value-container">
                            <h3 class="stat-value">{{ $totalSiswa }}</h3>
                            <p class="stat-subtitle">Santri Aktif</p>
                        </div>
                        
                        <div class="stat-trend {{ $totalSiswa > 0 ? 'up' : 'neutral' }}">
                            <i class="bi bi-{{ $totalSiswa > 0 ? 'graph-up' : 'dash-circle' }}"></i>
                            <span>{{ $totalSiswa > 0 ? 'Data Terkini' : 'Belum Ada Data' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Notifikasi -->
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <span class="stat-label">Notifikasi</span>
                            </div>
                            <div class="stat-icon bg-notif">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                        </div>
                        <div class="stat-value-container">
                            <h3 class="stat-value">{{ number_format($totalNotifikasi, 0, ',', '.') }}</h3>
                            <p class="stat-subtitle">Terkirim</p>
                        </div>
                        <div class="stat-trend neutral">
                            <i class="bi bi-dash-circle"></i>
                            <span>Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title-section">
                                <h5 class="chart-title">Statistik Kehadiran Siswa</h5>
                                <p class="chart-subtitle">Distribusi kehadiran per kelas sepanjang tahun</p>
                            </div>
                            
                            <div class="chart-controls">
                                <button class="btn btn-filter" data-range="1">1 Bulan</button>
                                <button class="btn btn-filter" data-range="6">6 Bulan</button>
                                <button class="btn btn-filter active" data-range="12">12 Bulan</button>
                            </div>
                        </div>

                        @if(count($kelasNames) > 0)
                            <div class="chart-legend">
                                @foreach($kelasNames as $index => $kelasName)
                                    @php
                                        $colors = ['#FF6B9D', '#9D6BFF', '#6B9DFF', '#FFB84D', '#4DFFB8'];
                                        $color = $colors[$index % count($colors)];
                                    @endphp
                                    <span class="legend-item">
                                        <span class="legend-dot" style="background: {{ $color }};"></span> 
                                        {{ $kelasName }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="chart-container w-100" style="position: relative; height:350px">
                            <canvas id="performanceChart"></canvas>
                            
                            @if(count($kelasNames) == 0)
                                <div class="empty-chart-message">
                                    <i class="bi bi-bar-chart" style="font-size: 64px;"></i>
                                    <p>Data kehadiran akan muncul setelah ada siswa terdaftar</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="logout-overlay" id="logoutOverlay">
        <div class="logout-modal">
            <h5>Apakah anda yakin ingin keluar?</h5>
            <div class="logout-actions">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
                <button class="btn-cancel" id="cancelLogout">Batal</button>
            </div>
        </div>
    </div>

    <script>
        window.dashboardData = {
            chartLabels: @json($chartLabels),
            chartDatasets: @json($chartDatasets),
            hasData: {{ count($kelasNames) > 0 ? 'true' : 'false' }}
        };
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
    <script src="{{ asset('assets/js/admin/dashboard_admin.js') }}"></script>
</body>
</html>