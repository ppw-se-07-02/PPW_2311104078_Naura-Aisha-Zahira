<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Pengguna - TPQ Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/data_pengguna.css') }}" rel="stylesheet">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo-section">
      <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="TPQ Logo" class="sidebar-logo">
    </div>
    
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
    <div class="container-full">
      
      <!-- Header Card dengan Back Button -->
      <div class="header-card">
        <a href="{{ route('admin.dashboard_admin') }}" class="btn-back">
          <i class="bi bi-chevron-left"></i>
        </a>
        <h4 class="header-title">Data Pengguna</h4>
      </div>

      <!-- Tab Navigation -->
      <ul class="nav nav-tabs custom-tabs" id="userTabs">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#siswa" id="tabSiswa">Siswa</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#guru" id="tabGuru">Guru</button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content">
        <!-- Tab Siswa -->
        <div class="tab-pane fade show active" id="siswa">
          <!-- Search & Add Button -->
          <div class="toolbar mb-4">
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" id="searchInputSiswa" class="form-control" placeholder="Cari nama siswa...">
            </div>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-add">
              <i class="bi bi-plus-circle"></i> Tambah Siswa
            </a>
          </div>
          
          <!-- Siswa Cards Grid -->
          <div class="row g-4" id="siswaContainer">
            @if(isset($siswaList) && count($siswaList) > 0)
              @foreach($siswaList as $siswa)
                <div class="col-md-6 col-lg-4 col-xl-3">
                  <div class="user-card">
                    <!-- ✅ FIX: Ambil foto dari tabel siswa, bukan user -->
                    <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('assets/img/default-avatar.png') }}" 
                         alt="{{ $siswa->nama_lengkap }}" 
                         class="user-avatar"
                         onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                    <h6 class="user-name">{{ $siswa->nama_lengkap }}</h6>
                    <p class="user-id">{{ $siswa->nis }} / {{ $siswa->kelas }}</p>
                    <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn btn-edit">Edit</a>
                  </div>
                </div>
              @endforeach
            @else
              <!-- Empty State -->
              <div class="col-12">
                <div class="empty-data-card">
                  <i class="bi bi-people"></i>
                  <h5>Belum Ada Data Siswa</h5>
                  <p>Data siswa akan muncul setelah ditambahkan ke database</p>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Tab Guru -->
        <div class="tab-pane fade" id="guru">
          <!-- Search & Add Button -->
          <div class="toolbar mb-4">
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" id="searchInput" class="form-control" placeholder="Cari nama guru...">
            </div>
            <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-add">
              <i class="bi bi-plus-circle"></i> Tambah Guru
            </a>
          </div>

          <!-- Guru Cards Grid -->
          <div class="row g-4" id="guruContainer">
            @if(isset($guruList) && count($guruList) > 0)
              @foreach($guruList as $guru)
                <div class="col-md-6 col-lg-4 col-xl-3">
                  <div class="user-card">
                    <img src="{{ $guru->foto ? asset('storage/' . $guru->foto) : asset('assets/img/default-avatar.png') }}" 
                         alt="{{ $guru->nama_lengkap }}" 
                         class="user-avatar"
                         onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                    <h6 class="user-name">{{ $guru->nama_lengkap }}</h6>
                    <p class="user-id">{{ $guru->nip }} / {{ $guru->kelas ?? 'Umum' }}</p>
                    <a href="{{ route('admin.guru.show', $guru->id) }}" class="btn btn-edit">Edit</a>
                  </div>
                </div>
              @endforeach
            @else
              <!-- Empty State -->
              <div class="col-12">
                <div class="empty-data-card">
                  <i class="bi bi-person-video3"></i>
                  <h5>Belum Ada Data Guru</h5>
                  <p>Data guru akan muncul setelah ditambahkan ke database</p>
                </div>
              </div>
            @endif
          </div>
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
  <script src="{{ asset('assets/js/admin/data_pengguna.js') }}"></script>
  
  <!-- ✅ Debug Script - Hapus setelah berhasil -->
  <script>
    console.log('Total Siswa:', {{ count($siswaList ?? []) }});
    console.log('Total Guru:', {{ count($guruList ?? []) }});
  </script>
</body>
</html>