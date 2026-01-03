<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Data Siswa - {{ $siswa->nama_lengkap }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/data_pengguna/siswa/edit_siswa.css') }}" rel="stylesheet">
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
        <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn-back">
          <i class="bi bi-chevron-left"></i>
        </a>
        <h4 class="header-title">Edit Data Siswa</h4>
      </div>

      <!-- Edit Form -->
      <form id="editSiswaForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="detail-container">
          <!-- Profile Card dengan Upload -->
          <div class="profile-card">
            <div class="profile-upload" onclick="document.getElementById('photoUpload').click()">
              <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=ffffff&color=22c55e&size=200&bold=true' }}" 
                   alt="{{ $siswa->nama_lengkap }}" 
                   class="profile-avatar" 
                   id="previewImage">
              <div class="upload-overlay">
                <i class="bi bi-camera-fill"></i>
              </div>
            </div>
            <input type="file" id="photoUpload" name="foto" accept="image/*" hidden>
            
            <div class="profile-info">
              <h5 class="profile-name" id="displayName">{{ $siswa->nama_lengkap }}</h5>
              <p class="profile-id" id="displayNisKelas">{{ $siswa->nis }} / Kelas {{ $siswa->kelas }}</p>
              <button type="button" class="btn-change-photo" onclick="document.getElementById('photoUpload').click()">
                <i class="bi bi-camera"></i> Ubah Foto
              </button>
            </div>
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
                <input type="text" class="form-control" name="nama" value="{{ $siswa->nama_lengkap }}" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">ID Siswa (NIS) <span class="required">*</span></label>
                <input type="text" class="form-control" name="idSiswa" value="{{ $siswa->nis }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Kelas <span class="required">*</span></label>
                <select class="form-select" name="kelas" required>
                  <option value="">Pilih Kelas</option>
                  <option value="A" {{ $siswa->kelas == 'A' ? 'selected' : '' }}>Kelas A</option>
                  <option value="B" {{ $siswa->kelas == 'B' ? 'selected' : '' }}>Kelas B</option>
                  <option value="C" {{ $siswa->kelas == 'C' ? 'selected' : '' }}>Kelas C</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                <select class="form-select" name="jenisKelamin" required>
                  <option value="">Pilih Jenis Kelamin</option>
                  <option value="Laki-laki" {{ $siswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="Perempuan" {{ $siswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tempat Lahir <span class="required">*</span></label>
                <input type="text" class="form-control" name="tempat_lahir" value="{{ $siswa->tempat_lahir }}" required>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Tanggal Lahir <span class="required">*</span></label>
                <input type="date" class="form-control" name="tanggal_lahir" value="{{ $siswa->tanggal_lahir }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Umur</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->age }} Tahun" disabled>
                <small class="form-text">Umur akan dihitung otomatis dari tanggal lahir</small>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                <textarea class="form-control" name="alamat" rows="3" required>{{ $siswa->alamat }}</textarea>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Nomor HP Orang Tua <span class="required">*</span></label>
                <input type="tel" class="form-control" name="no_hp" value="{{ $siswa->no_hp }}" required>
                <small class="form-text">Format: 08xxxxxxxxxx</small>
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
                <input type="text" class="form-control" name="username" value="{{ $siswa->user->username }}" required>
                <small class="form-text">Username minimal 5 karakter</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                <small class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.</small>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
              <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
              </a>
              <button type="submit" class="btn btn-primary" id="btnSubmit">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
              </button>
            </div>
          </div>
        </div>
      </form>
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
  <script>