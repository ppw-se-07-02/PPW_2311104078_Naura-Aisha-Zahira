<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Guru - TPQ</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link href="{{ asset('assets/css/guru/dashboard_guru.css') }}" rel="stylesheet" />  
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <div class="logo-section">
        <img
          src="{{ asset('assets/img/TPQSmart Logo.png') }}"
          alt="TPQ Logo"
          class="sidebar-logo"
        />
      </div>
      <!-- Navigation Menu -->
      <nav class="nav-menu">
        <a href="{{ route('guru.dashboard') }}" class="nav-item active" title="Dashboard">
          <i class="bi bi-house-door-fill"></i>
          <span class="nav-text">Dashboard</span>
        </a>
        <a href="{{ route('guru.presensi') }}" class="nav-item" title="Presensi">
          <i class="bi bi-calendar-check-fill"></i>
          <span class="nav-text">Presensi</span>
        </a>
        <a href="{{ route('guru.profil_siswa') }}" class="nav-item" title="Profil Siswa">
          <i class="bi bi-people-fill"></i>
          <span class="nav-text">Profil Siswa</span>
        </a>
        <a href="{{ route('guru.perkembangan') }}" class="nav-item" title="Perkembangan">
          <i class="bi bi-book-fill"></i>
          <span class="nav-text">Perkembangan</span>
        </a>
        <a href="{{ route('guru.laporan_evaluasi') }}" class="nav-item" title="Laporan Evaluasi">
          <i class="bi bi-bar-chart-fill"></i>
          <span class="nav-text">Laporan Evaluasi</span>
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
        <div class="user-profile">
          <div class="user-info">
            <div class="profile-dropdown">
              <div class="profile-meta">
                <span class="profile-class">Kelas {{ $guru->kelas ?? 'Belum Ada' }}</span>
                <span class="profile-year">2024/2025</span>
              </div>
            </div>
            <span class="user-name">{{ $guru->nama_lengkap }}</span>
            <span class="user-role">Guru</span>
          </div>
          <img
            src="{{ $guru->foto ? asset('storage/' . $guru->foto) : asset('assets/img/default-avatar.png') }}"
            alt="User"
            class="user-avatar"
          />
          <button class="btn-dropdown">
            <i class="bi bi-chevron-down"></i>
          </button>
        </div>
      </header>

      <!-- Stats Cards -->
      <div class="container-fluid px-4 py-4">
        <div class="row g-3 mb-4">
          <!-- Card 1: Total Siswa di Kelas -->
          <div class="col-md-6 col-lg-3">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Total Siswa di Kelas</span>
                <div class="stat-icon bg-primary">
                  <i class="bi bi-people-fill"></i>
                </div>
              </div>
              <h3 class="stat-value">{{ $totalSiswa }} Siswa</h3>
              <div class="stat-info">
                <i class="bi bi-info-circle"></i>
                <span>Kelas {{ $guru->kelas }}</span>
              </div>
            </div>
          </div>

          <!-- Card 2: Hadir Hari Ini -->
          <div class="col-md-6 col-lg-3">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Hadir Hari Ini</span>
                <div class="stat-icon bg-success">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
              </div>
              <h3 class="stat-value">{{ $siswaHadirHariIni }} Siswa</h3>
              <div class="stat-info {{ $persentaseKehadiran > 0 ? 'success' : '' }}">
                <i class="bi bi-{{ $persentaseKehadiran > 0 ? 'arrow-up' : 'dash' }}-circle"></i>
                <span>{{ $persentaseKehadiran }}% Kehadiran</span>
              </div>
            </div>
          </div>

          <!-- Card 3: Perlu Evaluasi -->
          <div class="col-md-6 col-lg-3">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Perlu Evaluasi</span>
                <div class="stat-icon bg-warning">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
              </div>
              <h3 class="stat-value">{{ $siswaPerluEvaluasi }} Siswa</h3>
              <div class="stat-info warning">
                <i class="bi bi-clock-fill"></i>
                <span>{{ $siswaPerluEvaluasi > 0 ? 'Perlu Perhatian' : 'Semua Baik' }}</span>
              </div>
            </div>
          </div>

          <!-- Card 4: Progress Minggu Ini -->
          <div class="col-md-6 col-lg-3">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Perkembangan Terisi</span>
                <div class="stat-icon bg-info">
                  <i class="bi bi-graph-up-arrow"></i>
                </div>
              </div>
              <h3 class="stat-value">{{ $catatanPerkembanganMingguIni }} Catatan</h3>
              <div class="stat-info info">
                <i class="bi bi-star-fill"></i>
                <span>{{ $catatanPerkembanganMingguIni > 0 ? 'Minggu Ini' : 'Belum Ada Input' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Aktivitas Terbaru Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="recent-activity-card">
              <div class="activity-header">
                <div>
                  <h5 class="activity-title">Aktivitas Terbaru</h5>
                  <p class="activity-subtitle">
                    Kegiatan pembelajaran hari ini
                  </p>
                </div>
              </div>

              <div class="activity-list">
                @if(count($aktivitasTerbaru) > 0)
                  @foreach($aktivitasTerbaru as $aktivitas)
                    <div class="activity-item">
                      <div class="activity-icon">
                        <i class="bi bi-{{ $aktivitas['icon'] }}"></i>
                      </div>
                      <div class="activity-content">
                        <p class="activity-text">{{ $aktivitas['text'] }}</p>
                        <span class="activity-time">{{ $aktivitas['time'] }}</span>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="text-center py-5">
                    <div class="mb-3">
                      <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #dee2e6;"></i>
                    </div>
                    <h6 class="text-muted">Belum ada aktivitas hari ini</h6>
                    <p class="small text-secondary">Aktivitas seperti presensi dan penilaian <br> akan muncul di sini setelah Anda menginput data.</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>


    <!-- Logout Confirmation Modal -->
    <div class="logout-overlay" id="logoutOverlay">
      <div class="logout-modal">
        <h5>Apakah anda yakin<br />ingin keluar?</h5>
        <div class="logout-actions">
          <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout" id="confirmLogout">Keluar</button>
          </form>
          <button class="btn-cancel" id="cancelLogout">Batal</button>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/guru/dashboard_guru.js') }}"></script>
  </body>
</html>