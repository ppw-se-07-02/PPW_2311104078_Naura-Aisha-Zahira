<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Siswa - TPQ Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link href="{{ asset('assets/css/guru/profil_siswa.css') }}" rel="stylesheet" />
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
      <nav class="nav-menu">
        <a href="{{ route('guru.dashboard') }}" 
        class="nav-item {{ Request::is('guru/dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="bi bi-house-door-fill"></i>
            <span class="nav-text">Dashboard</span>
        </a>

        <a href="{{ route('guru.presensi') }}" 
        class="nav-item {{ Request::is('guru/presensi') ? 'active' : '' }}" title="Presensi">
            <i class="bi bi-calendar-check-fill"></i>
            <span class="nav-text">Presensi</span>
        </a>

        <a href="{{ route('guru.profil_siswa') }}" 
        class="nav-item {{ Request::is('guru/profil-siswa') ? 'active' : '' }}" title="Profil Siswa">
            <i class="bi bi-people-fill"></i>
            <span class="nav-text">Profil Siswa</span>
        </a>

        <a href="{{ route('guru.perkembangan') }}" 
        class="nav-item {{ Request::is('guru/perkembangan') ? 'active' : '' }}" title="Perkembangan">
            <i class="bi bi-book-fill"></i>
            <span class="nav-text">Perkembangan</span>
        </a>

        <a href="{{ route('guru.laporan_evaluasi') }}" 
        class="nav-item {{ Request::is('guru/laporan-evaluasi') ? 'active' : '' }}" title="Laporan Evaluasi">
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
      <div class="profil-header">
        <div class="header-left">
          <button
            class="btn-back"
            onclick="window.location.href='{{ route('guru.dashboard') }}'">
            <i class="bi bi-chevron-left"></i>
          </button>
          <h1 class="header-title">Profil Siswa - Kelas {{ $guru->kelas }}</h1>
        </div>
        <div class="header-right">
          <span class="total-siswa">
            <i class="bi bi-people-fill"></i>
            <span id="totalSiswa">{{ $totalSiswa }}</span> Siswa
          </span>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="search-container">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input
            type="text"
            id="searchInput"
            placeholder="Cari nama siswa atau NIS..."
            class="search-input"
          />
          <button class="btn-clear" id="btnClear" style="display: none">
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>
      </div>

      <!-- Students Grid -->
      <div class="students-container">
        <div class="row g-3" id="studentsGrid">
          @if($siswaList->count() > 0)
            @foreach($siswaList as $siswa)
              <div class="col-6 col-md-4 col-lg-3">
                <div class="student-card" data-student-id="{{ $siswa->nis }}">
                  <div class="student-photo">
                    <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=2EAF7D&color=fff&size=200' }}" 
                         alt="{{ $siswa->nama_lengkap }}"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=2EAF7D&color=fff&size=200'">
                  </div>
                  <div class="student-name">{{ $siswa->nama_lengkap }}</div>
                  <div class="student-id">NIS: {{ $siswa->nis }}</div>
                </div>
              </div>
            @endforeach
          @else
            <div class="col-12">
              <div class="no-results">
                <i class="bi bi-people"></i>
                <h5>Belum ada siswa di kelas ini</h5>
                <p>Siswa akan muncul di sini setelah ditambahkan oleh admin</p>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- No Results Message (untuk search) -->
      <div class="no-results" id="noResults" style="display: none">
        <i class="bi bi-search"></i>
        <h5>Tidak ada siswa ditemukan</h5>
        <p>Coba gunakan kata kunci lain</p>
      </div>
    </div>

    <!-- Modal Biodata Siswa -->
    <div
      class="modal fade"
      id="biodataModal"
      tabindex="-1"
      aria-labelledby="biodataModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="biodataModalLabel">Biodata Siswa</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="biodata-content">
              <div class="biodata-photo">
                <img id="modalPhoto" src="" alt="Foto Siswa" />
              </div>
              <div class="biodata-info">
                <div class="info-row">
                  <span class="info-label">Nama</span>
                  <span class="info-value" id="modalNama">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">NIS</span>
                  <span class="info-value" id="modalId">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Jenis Kelamin</span>
                  <span class="info-value" id="modalGender">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Tanggal Lahir</span>
                  <span class="info-value" id="modalTanggalLahir">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Umur</span>
                  <span class="info-value" id="modalUmur">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Nama Orang Tua</span>
                  <span class="info-value" id="modalOrangTua">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Nomor WA Orang Tua</span>
                  <span class="info-value" id="modalWA">-</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Alamat</span>
                  <span class="info-value" id="modalAlamat">-</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Tutup
            </button>
            <button
              type="button"
              class="btn btn-primary"
              id="btnContactParent"
            >
              <i class="bi bi-whatsapp"></i> Hubungi Orang Tua
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
            <button type="submit" class="btn-logout" id="confirmLogout">Keluar</button>
          </form>
          <button class="btn-cancel" id="cancelLogout">Batal</button>
        </div>
      </div>
    </div>

    <!-- ✅ Pass data siswa ke JavaScript -->
    <script>
      // Data siswa dari Laravel
      window.siswaData = @json($siswaData);
      window.totalSiswa = {{ $totalSiswa }};
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/guru/profil_siswa.js') }}"></script>
  </body>
</html>