<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Notifikasi - TPQ Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/riwayat_notifikasi.css') }}" rel="stylesheet">
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
        <h4 class="header-title">Riwayat Notifikasi</h4>
        <button class="btn-primary-action ms-auto" id="btnKirimBaru">
          <i class="bi bi-send-fill"></i> Kirim Pesan Baru
        </button>
      </div>

      <!-- Stats Cards Row -->
      <div class="stats-row">
        <div class="stat-mini-card">
          <div class="stat-mini-icon success">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <div class="stat-mini-content">
            <span class="stat-mini-label">Berhasil</span>
            <h3 class="stat-mini-value" id="totalBerhasil">{{ $totalBerhasil ?? 0 }}</h3>
          </div>
        </div>

        <div class="stat-mini-card">
          <div class="stat-mini-icon danger">
            <i class="bi bi-x-circle-fill"></i>
          </div>
          <div class="stat-mini-content">
            <span class="stat-mini-label">Gagal</span>
            <h3 class="stat-mini-value" id="totalGagal">{{ $totalGagal ?? 0 }}</h3>
          </div>
        </div>

        <div class="stat-mini-card">
          <div class="stat-mini-icon info">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div class="stat-mini-content">
            <span class="stat-mini-label">Total Pesan</span>
            <h3 class="stat-mini-value" id="totalPesan">{{ $totalNotifikasi ?? 0 }}</h3>
          </div>
        </div>

        <div class="stat-mini-card">
          <div class="stat-mini-icon warning">
            <i class="bi bi-clock-history"></i>
          </div>
          <div class="stat-mini-content">
            <span class="stat-mini-label">Hari Ini</span>
            <h3 class="stat-mini-value" id="totalHariIni">{{ $hariIni ?? 0 }}</h3>
          </div>
        </div>
      </div>

      <!-- Filter & Action Card -->
      <div class="filter-action-card">
        <div class="filter-section">
          <div class="filter-group">
            <label class="filter-label">
              <i class="bi bi-funnel-fill"></i> Filter Penerima:
            </label>
            <div class="filter-chips">
              <button class="filter-chip active" data-filter="penerima" data-value="all">
                Semua
              </button>
              <button class="filter-chip" data-filter="penerima" data-value="Kelas A">
                Kelas A
              </button>
              <button class="filter-chip" data-filter="penerima" data-value="Kelas B">
                Kelas B
              </button>
              <button class="filter-chip" data-filter="penerima" data-value="Kelas C">
                Kelas C
              </button>
            </div>
          </div>

          <div class="filter-group">
            <label class="filter-label">
              <i class="bi bi-activity"></i> Status:
            </label>
            <div class="filter-chips">
              <button class="filter-chip active" data-filter="status" data-value="all">
                Semua
              </button>
              <button class="filter-chip" data-filter="status" data-value="berhasil">
                <i class="bi bi-check-circle"></i> Berhasil
              </button>
              <button class="filter-chip" data-filter="status" data-value="gagal">
                <i class="bi bi-x-circle"></i> Gagal
              </button>
            </div>
          </div>
        </div>

        <div class="action-section">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchNotifikasi" class="form-control" placeholder="Cari pesan atau penerima...">
          </div>
          <div class="action-buttons">
            <button class="btn btn-warning" id="btnKirimUlang">
              <i class="bi bi-arrow-clockwise"></i> 
              <span class="btn-text">Kirim Ulang</span> 
              (<span id="countGagal">0</span>)
            </button>
            <button class="btn btn-success" id="btnExport">
              <i class="bi bi-download"></i> 
              <span class="btn-text">Export</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table table-notifikasi">
            <thead>
              <tr>
                <th width="70">NO</th>
                <th width="120">TANGGAL</th>
                <th width="150">PENERIMA</th>
                <th width="130">STATUS</th>
                <th>PESAN</th>
                <th width="120">AKSI</th>
              </tr>
            </thead>
            <tbody id="notifikasiTableBody">
              @if(isset($notifikasiList) && count($notifikasiList) > 0)
                @foreach($notifikasiList as $notif)
                  <tr data-id="{{ $notif->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($notif->tanggal)->format('d/m/Y') }}</td>
                    <td><strong>{{ $notif->penerima }}</strong></td>
                    <td>
                      <span class="status-badge {{ $notif->status }}">
                        @if($notif->status == 'berhasil')
                          <i class="bi bi-check-circle-fill"></i> Berhasil
                        @else
                          <i class="bi bi-x-circle-fill"></i> Gagal
                        @endif
                      </span>
                    </td>
                    <td>
                      <span class="pesan-text">{{ $notif->pesan }}</span>
                    </td>
                    <td>
                      <div class="action-btns">
                        <button class="btn-action view" onclick="viewDetail({{ $notif->id }})" title="Lihat Detail">
                          <i class="bi bi-eye-fill"></i>
                        </button>
                        <button class="btn-action resend" onclick="resendNotif({{ $notif->id }})" 
                                title="Kirim Ulang" {{ $notif->status == 'berhasil' ? 'disabled' : '' }}>
                          <i class="bi bi-arrow-clockwise"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="empty-table">
                      <i class="bi bi-inbox"></i>
                      <h5>Belum Ada Riwayat Notifikasi</h5>
                      <p>Notifikasi yang dikirim akan muncul di sini</p>
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
        <p class="summary-text">
          Menampilkan <span id="displayCount">0</span> dari <span id="totalCount">0</span> notifikasi
        </p>
        <div class="summary-badges">
          <span class="badge-summary success">
            <i class="bi bi-check-circle-fill"></i> Berhasil: <strong id="summaryBerhasil">0</strong>
          </span>
          <span class="badge-summary danger">
            <i class="bi bi-x-circle-fill"></i> Gagal: <strong id="summaryGagal">0</strong>
          </span>
        </div>
      </div>

    </div>
  </div>

  <!-- Modal Kirim Pesan Baru -->
  <div class="modal fade" id="kirimPesanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content modal-custom">
        <div class="modal-header-custom">
          <div class="modal-icon">
            <i class="bi bi-send-fill"></i>
          </div>
          <h5 class="modal-title-custom">Kirim Pesan Notifikasi</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body-custom">
          <form id="formKirimPesan">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="bi bi-people-fill"></i> Penerima <span class="text-danger">*</span>
              </label>
              <select class="form-control-custom" id="penerima" required>
                <option value="">Pilih Penerima</option>
                <option value="Kelas A">Kelas A</option>
                <option value="Kelas B">Kelas B</option>
                <option value="Kelas C">Kelas C</option>
                <option value="Semua">Semua Kelas</option>
              </select>
            </div>

            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="bi bi-envelope-fill"></i> Pesan <span class="text-danger">*</span>
              </label>
              <textarea class="form-control-custom" id="pesan" rows="5" 
                        placeholder="Tulis pesan notifikasi di sini..." required></textarea>
              <small class="text-muted">Maksimal 500 karakter</small>
            </div>
          </form>
        </div>
        <div class="modal-footer-custom">
          <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Batal
          </button>
          <button type="button" class="btn-modal-primary" id="btnKirim">
            <i class="bi bi-send-fill"></i> Kirim Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal View Detail -->
  <div class="modal fade" id="viewDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content modal-custom">
        <div class="modal-header-custom">
          <div class="modal-icon">
            <i class="bi bi-info-circle-fill"></i>
          </div>
          <h5 class="modal-title-custom">Detail Notifikasi</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body-custom" id="detailContent">
          <!-- Content will be inserted by JavaScript -->
        </div>
        <div class="modal-footer-custom">
          <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Tutup
          </button>
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
    window.notifikasiData = {
      notifikasiList: @json($notifikasiList ?? []),
      totalBerhasil: {{ $totalBerhasil ?? 0 }},
      totalGagal: {{ $totalGagal ?? 0 }},
      totalNotifikasi: {{ $totalNotifikasi ?? 0 }}
    };
  </script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
  <script src="{{ asset('assets/js/admin/riwayat_notifikasi.js') }}"></script>
</body>
</html>