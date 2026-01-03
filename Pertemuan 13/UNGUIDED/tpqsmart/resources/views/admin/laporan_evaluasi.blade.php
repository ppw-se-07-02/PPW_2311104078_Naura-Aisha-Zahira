<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Evaluasi - TPQ Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/laporan_evaluasi.css') }}" rel="stylesheet">
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
        <h4 class="header-title">Laporan Evaluasi</h4>
      </div>

      <!-- Stats Cards Row -->
      <div class="stats-row">
        <!-- Total Siswa -->
        <div class="stat-card total">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Total Siswa</p>
              <h2 class="stat-value" id="totalSiswa">0</h2>
            </div>
            <div class="stat-icon">
              <i class="bi bi-people-fill"></i>
            </div>
          </div>
        </div>

        <!-- Melanjutkan -->
        <div class="stat-card melanjutkan">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Melanjutkan</p>
              <h2 class="stat-value" id="totalMelanjutkan">0</h2>
              <p class="stat-percentage" id="persenMelanjutkan">0% dari total</p>
            </div>
            <div class="stat-icon">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
          </div>
        </div>

        <!-- Mengulangi -->
        <div class="stat-card mengulangi">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Mengulangi</p>
              <h2 class="stat-value" id="totalMengulangi">0</h2>
              <p class="stat-percentage" id="persenMengulangi">0% dari total</p>
            </div>
            <div class="stat-icon">
              <i class="bi bi-book-fill"></i>
            </div>
          </div>
        </div>

        <!-- Progress Rate -->
        <div class="stat-card progress-rate">
          <div class="stat-content">
            <div class="stat-info">
              <p class="stat-label">Progress Rate</p>
              <h2 class="stat-value" id="progressRate">0%</h2>
              <p class="stat-percentage">Tingkat keberhasilan</p>
            </div>
            <div class="stat-icon">
              <i class="bi bi-award-fill"></i>
            </div>
          </div>
        </div>
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
            <input type="date" class="form-control date-input" id="evaluasiDate" value="{{ $selectedDate ?? date('Y-m-d') }}">
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
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button class="filter-btn active" data-status="all">
          <i class="bi bi-funnel"></i> Semua (<span id="countAll">0</span>)
        </button>
        <button class="filter-btn" data-status="melanjutkan">
          <i class="bi bi-arrow-up-circle"></i> Melanjutkan (<span id="countMelanjutkan">0</span>)
        </button>
        <button class="filter-btn" data-status="mengulangi">
          <i class="bi bi-arrow-repeat"></i> Mengulangi (<span id="countMengulangi">0</span>)
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
                <th width="180">TILAWATI</th>
                <th width="160">KEMAMPUAN</th>
                <th width="100">AKSI</th>
              </tr>
            </thead>
            <tbody id="evaluasiTableBody">
              @if(isset($evaluasiData) && count($evaluasiData) > 0)
                @foreach($evaluasiData as $evaluasi)
                  <tr>
                    <td>{{ $evaluasi->student_id }}</td>
                    <td><strong>{{ $evaluasi->student_name }}</strong></td>
                    <td><span class="tilawati-text">{{ $evaluasi->tilawati ?? '-' }}</span></td>
                    <td>
                      <span class="status-badge {{ $evaluasi->kemampuan }}">
                        {{ ucfirst($evaluasi->kemampuan) }}
                      </span>
                    </td>
                    <td>
                      <button class="btn-edit-presensi" onclick="viewDetail({{ $evaluasi->id }}, '{{ $evaluasi->student_name }}')">
                        <i class="bi bi-eye-fill"></i>
                      </button>
                      <button class="btn-edit-presensi" onclick="editEvaluasi({{ $evaluasi->id }}, '{{ $evaluasi->student_name }}')">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="empty-table">
                      <i class="bi bi-clipboard-data"></i>
                      <h5>Belum Ada Data Evaluasi</h5>
                      <p>Pilih kelas untuk melihat data evaluasi siswa</p>
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
          <span class="badge-summary melanjutkan">
            <i class="bi bi-arrow-up-circle-fill"></i> Melanjutkan: <strong id="summaryMelanjutkan">0</strong>
          </span>
          <span class="badge-summary mengulangi">
            <i class="bi bi-arrow-repeat"></i> Mengulangi: <strong id="summaryMengulangi">0</strong>
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


{{-- ======================================= --}}
{{-- PASS DATA KE JAVASCRIPT - PENTING!!! --}}
{{-- ======================================= --}}
<script>
    // Data dari Backend (Laravel Controller)
    window.evaluasiData = {
        selectedClass: @json($selectedClass ?? null),
        selectedDate: @json($selectedDate ?? date('Y-m-d')),
        evaluasiList: @json($evaluasiData ?? []),
        classList: @json($classList ?? []),
        totalStudents: {{ $totalStudents ?? 0 }},
        countMelanjutkan: {{ $countMelanjutkan ?? 0 }},
        countMengulangi: {{ $countMengulangi ?? 0 }}
    };
    
    // Debug - Cek apakah data masuk
    console.log('=== LAPORAN EVALUASI DATA ===');
    console.log('Selected Class:', window.evaluasiData.selectedClass);
    console.log('Selected Date:', window.evaluasiData.selectedDate);
    console.log('Total Students:', window.evaluasiData.totalStudents);
    console.log('Evaluasi List:', window.evaluasiData.evaluasiList);
    console.log('Class List:', window.evaluasiData.classList);
    console.log('============================');
</script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
  <script src="{{ asset('assets/js/admin/laporan_evaluasi.js') }}"></script>
</body>
</html>