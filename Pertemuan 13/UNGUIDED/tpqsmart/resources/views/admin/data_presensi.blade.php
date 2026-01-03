<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Presensi - TPQ Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/data_presensi.css') }}" rel="stylesheet">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo-section">
      <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="TPQ Logo" class="sidebar-logo">
    </div>
    
    <!-- Navigation Menu -->
    <nav class="nav-menu">
      <a href="{{ route('admin.dashboard_admin') }}" class="nav-item {{ Request::is('admin/dashboard-admin') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        <span class="nav-text">Beranda</span>
      </a>

      <a href="{{ route('admin.data_pengguna') }}" class="nav-item {{ Request::is('admin/data-pengguna*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i>
        <span class="nav-text">Data Pengguna</span>
      </a>

      <a href="{{ route('admin.data_presensi') }}" class="nav-item {{ Request::is('admin/data-presensi*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check-fill"></i>
        <span class="nav-text">Data Presensi</span>
      </a>

      <a href="{{ route('admin.laporan_evaluasi') }}" class="nav-item {{ Request::is('admin/laporan-evaluasi*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i>
        <span class="nav-text">Laporan Evaluasi</span>
      </a>

      <a href="{{ route('admin.riwayat_notifikasi') }}" class="nav-item {{ Request::is('admin/riwayat-notifikasi*') ? 'active' : '' }}">
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
    <!-- Main Container - FULL SCREEN -->
    <div class="container-full">
      
      <!-- Header Card dengan Back Button -->
      <div class="header-card">
        <a href="{{ route('admin.dashboard_admin') }}" class="btn-back">
          <i class="bi bi-chevron-left"></i>
        </a>
        <h4 class="header-title">Data Presensi</h4>
      </div>

      <!-- Class Info Card -->
      <div class="class-info-card">
        <div class="class-info-left">
          <div class="class-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <div>
            <h5 class="class-name" id="selectedClassName">{{ $selectedClass ?? 'Pilih Kelas' }}</h5>
            <p class="class-count" id="classStudentCount">{{ $totalStudents ?? 0 }} Siswa</p>
          </div>
        </div>
        <div class="class-controls">
          <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="classDropdown" data-bs-toggle="dropdown">
              <i class="bi bi-building"></i> Ganti Kelas
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              @if(isset($classList) && count($classList) > 0)
                @foreach($classList as $class)
                  <li>
                    <a class="dropdown-item" href="#" onclick="selectClass('{{ $class->id }}', '{{ $class->nama }}'); return false;">
                      <strong>{{ $class->nama }}</strong>
                      <small class="d-block text-muted">{{ $class->students_count ?? 0 }} Siswa</small>
                    </a>
                  </li>
                @endforeach
              @else
                <li><span class="dropdown-item text-muted">Belum ada kelas</span></li>
              @endif
            </ul>
          </div>
          <div class="date-picker-wrapper">
            <input type="date" class="form-control date-input" id="presensiDate" value="{{ $selectedDate ?? date('Y-m-d') }}">
          </div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar-presensi">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="searchSiswa" class="form-control" placeholder="Cari nama siswa...">
        </div>
        <div class="action-buttons">
          <button class="btn btn-success" id="btnExport">
            <i class="bi bi-download"></i> Export Data
          </button>
          <button class="btn btn-danger" id="btnReset">
            <i class="bi bi-arrow-clockwise"></i> Reset Presensi
          </button>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button class="filter-btn active" data-status="all">
          <i class="bi bi-funnel"></i> Semua (<span id="countAll">0</span>)
        </button>
        <button class="filter-btn" data-status="hadir">
          <i class="bi bi-check-circle"></i> Hadir (<span id="countHadir">0</span>)
        </button>
        <button class="filter-btn" data-status="izin">
          <i class="bi bi-file-text"></i> Izin (<span id="countIzin">0</span>)
        </button>
        <button class="filter-btn" data-status="sakit">
          <i class="bi bi-bandaid"></i> Sakit (<span id="countSakit">0</span>)
        </button>
        <button class="filter-btn" data-status="alpha">
          <i class="bi bi-x-circle"></i> Alpha (<span id="countAlpha">0</span>)
        </button>
      </div>

      <!-- Table Card -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table table-presensi">
            <thead>
              <tr>
                <th width="80">ID</th>
                <th>NAMA</th>
                <th width="140">STATUS</th>
                <th width="120">WAKTU</th>
                <th width="80">AKSI</th>
              </tr>
            </thead>
            <tbody id="presensiTableBody">
              @if(isset($presensiData) && count($presensiData) > 0)
                @foreach($presensiData as $presensi)
                  <tr>
                    <td>{{ $presensi->student_id }}</td>
                    <td><strong>{{ $presensi->student_name }}</strong></td>
                    <td>
                      <span class="status-badge {{ $presensi->status }}">
                        {{ ucfirst($presensi->status) }}
                      </span>
                    </td>
                    <td>
                      @if($presensi->waktu)
                        <span class="waktu-presensi">
                          <i class="bi bi-clock"></i> {{ $presensi->waktu }}
                        </span>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      <button class="btn-edit-presensi" onclick="editPresensi({{ $presensi->id }})">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="empty-table">
                      <i class="bi bi-calendar-x"></i>
                      <h5>Belum Ada Data Presensi</h5>
                      <p>Pilih kelas untuk melihat data presensi siswa</p>
                    </div>
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

      <!-- Summary Card -->
      <div class="summary-card">
        <div class="summary-left">
          <p class="summary-text">
            Menampilkan <span id="displayCount">0</span> dari <span id="totalCount">0</span> siswa
          </p>
        </div>
        <div class="summary-badges">
          <span class="badge-summary hadir">
            <i class="bi bi-check-circle-fill"></i> Hadir: <strong id="summaryHadir">0</strong>
          </span>
          <span class="badge-summary izin">
            <i class="bi bi-file-text-fill"></i> Izin: <strong id="summaryIzin">0</strong>
          </span>
          <span class="badge-summary sakit">
            <i class="bi bi-bandaid-fill"></i> Sakit: <strong id="summarySakit">0</strong>
          </span>
          <span class="badge-summary alpha">
            <i class="bi bi-x-circle-fill"></i> Alpha: <strong id="summaryAlpha">0</strong>
          </span>
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
          <button type="submit" class="btn-logout">Keluar</button>
        </form>
        <button class="btn-cancel" id="cancelLogout">Batal</button>
      </div>
    </div>
  </div>

  {{-- Pass data ke JavaScript --}}
  <script>
    window.presensiData = {
      selectedClass: @json($selectedClass ?? null),
      selectedDate: @json($selectedDate ?? date('Y-m-d')),
      presensiList: @json($presensiData ?? []),
      classList: @json($classList ?? [])
    };
  </script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
  <script src="{{ asset('assets/js/admin/data_presensi.js') }}"></script>
</body>
</html>