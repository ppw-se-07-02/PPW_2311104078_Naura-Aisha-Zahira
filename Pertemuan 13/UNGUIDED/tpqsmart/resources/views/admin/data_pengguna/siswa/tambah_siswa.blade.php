<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tambah Pengguna Siswa - TPQ Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/data_pengguna/siswa/tambah_siswa.css') }}" rel="stylesheet">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo-section">
      <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="TPQ Logo" class="sidebar-logo">
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
    <div class="container-full">
      
      <!-- Header Card dengan Back Button -->
      <div class="header-card">
        <a href="{{ route('admin.data_pengguna') }}" class="btn-back">
          <i class="bi bi-chevron-left"></i>
        </a>
        <h4 class="header-title">Tambah Pengguna Siswa</h4>
      </div>

      <!-- Form -->
      <form id="tambahSiswaForm" action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="detail-container">
          <!-- Profile Upload Card -->
          <div class="profile-upload-card">
            <p class="upload-instruction">Upload Foto Profil Siswa</p>
            <div class="profile-upload-container" id="uploadArea" onclick="document.getElementById('photoUpload').click()">
              <div id="uploadPlaceholder">
                <i class="bi bi-cloud-upload"></i>
                <span>Upload</span>
              </div>
              <img src="" id="previewImage" class="profile-avatar" style="display: none;">
            </div>
            <input type="file" id="photoUpload" name="foto" hidden accept="image/*">
            <button type="button" class="btn-choose-photo" onclick="document.getElementById('photoUpload').click()">
              <i class="bi bi-image"></i> Pilih Foto
            </button>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.85rem; margin-top: 10px; position: relative; z-index: 1;">
              <i class="bi bi-info-circle"></i> Maksimal 2MB (JPG, PNG, GIF)
            </p>
          </div>

          <!-- Informasi Pribadi -->
          <div class="form-section">
            <h6 class="section-title">
              <i class="bi bi-person-circle"></i>
              Informasi Pribadi
            </h6>
            
            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap siswa" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">ID Siswa (NIS) <span class="required">*</span></label>
                <input type="text" class="form-control" name="idSiswa" placeholder="Contoh: 001" required>
                <small class="form-text"><i class="bi bi-info-circle"></i> Nomor induk siswa yang unik</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Kelas <span class="required">*</span></label>
                <select class="form-select" name="kelas" required>
                  <option value="" selected disabled>Pilih Kelas</option>
                  <option value="A">Kelas A</option>
                  <option value="B">Kelas B</option>
                  <option value="C">Kelas C</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                <select class="form-select" name="jenisKelamin" required>
                  <option value="" selected disabled>Pilih Jenis Kelamin</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                <input type="text" class="form-control" name="tempat_lahir" placeholder="Contoh: Jakarta" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                <input type="date" class="form-control" name="tanggal_lahir" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Nomor HP Orang Tua <span class="required">*</span></label>
                <input type="tel" class="form-control" name="no_hp" placeholder="08123456789" required>
                <small class="form-text"><i class="bi bi-phone"></i> Format: 08xxxxxxxxxx</small>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan alamat lengkap siswa" required></textarea>
              </div>
            </div>
          </div>

          <!-- Informasi Akun -->
          <div class="form-section">
            <h6 class="section-title">
              <i class="bi bi-shield-lock"></i>
              Informasi Akun
            </h6>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Username <span class="required">*</span></label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                <small class="form-text"><i class="bi bi-person-badge"></i> Username minimal 5 karakter</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                <small class="form-text"><i class="bi bi-key"></i> Password minimal 8 karakter</small>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                <i class="bi bi-x-circle"></i> Batal
              </button>
              <button type="submit" class="btn btn-primary" id="btnSubmit">
                <i class="bi bi-check-circle"></i> Tambahkan Siswa
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Cancel Modal -->
  <div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body text-center p-4">
          <i class="bi bi-exclamation-triangle text-warning" style="font-size: 64px;"></i>
          <h5 class="mt-3 fw-bold">Konfirmasi Batal</h5>
          <p class="text-muted">Apakah Anda yakin? Semua data yang sudah diisi akan hilang dan tidak tersimpan.</p>
          <div class="d-flex justify-content-center gap-2 mt-4">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tidak, Lanjut Isi</button>
            <a href="{{ route('admin.data_pengguna') }}" class="btn btn-danger px-4">Ya, Batalkan</a>
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/admin/sidebar_nav.js') }}"></script>
  <script src="{{ asset('assets/js/admin/data_pengguna/siswa/tambah_siswa.js') }}"></script>
</body>
</html>