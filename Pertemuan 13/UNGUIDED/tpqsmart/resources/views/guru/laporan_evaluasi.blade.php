<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Evaluasi - TPQ Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link href="{{ asset('assets/css/guru/laporan_evaluasi.css') }}" rel="stylesheet" />
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
      <div class="laporan-header">
        <div class="header-left">
          <button
            class="btn-back"
            onclick="window.location.href='{{ route('guru.dashboard') }}'">
            <i class="bi bi-chevron-left"></i>
          </button>
          <h1 class="header-title">Laporan Evaluasi - Kelas {{ $guru->kelas }}</h1>
        </div>
        <div class="header-right">
          <div class="date-badge" id="currentDate" style="cursor: pointer">
            <i class="bi bi-calendar3"></i>
            <span>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="search-container">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input
            type="text"
            id="searchInput"
            placeholder="Cari nama siswa..."
            class="search-input"
          />
          <button class="btn-clear" id="btnClear" style="display: none">
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>
      </div>

      <!-- Evaluasi Table -->
      <div class="table-container">
        <div class="table-header">
          <h5>Daftar Evaluasi Siswa</h5>
          <div class="header-stats">
            <span class="stat-item">
              <i class="bi bi-check-circle-fill text-success"></i>
              <span id="countMelanjutkan">{{ $countMelanjutkan }}</span> Melanjutkan
            </span>
            <span class="stat-item">
              <i class="bi bi-arrow-repeat text-warning"></i>
              <span id="countMengulangi">{{ $countMengulangi }}</span> Mengulangi
            </span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="evaluasi-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Tilawati</th>
                <th>Kemampuan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="evaluasiList">
              @forelse($evaluasiList as $evaluasi)
                <tr data-id="{{ $evaluasi['id'] }}" data-nama="{{ strtolower($evaluasi['student_name']) }}" data-kemampuan="{{ $evaluasi['kemampuan'] }}">
                  <td>{{ $evaluasi['student_id'] }}</td>
                  <td><strong>{{ $evaluasi['student_name'] }}</strong></td>
                  <td><span class="tilawati-text">{{ $evaluasi['tilawati'] }}</span></td>
                  <td>
                    <span class="status-kemampuan status-{{ $evaluasi['kemampuan'] }}">
                      {{ ucfirst($evaluasi['kemampuan']) }}
                    </span>
                  </td>
                  <td>
                    <button class="btn-detail" 
                            data-siswa-id="{{ $evaluasi['id'] }}"
                            data-siswa-nama="{{ $evaluasi['student_name'] }}"
                            data-siswa-nis="{{ $evaluasi['student_id'] }}"
                            onclick="viewDetail(this)" 
                            title="Lihat Detail">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <i class="bi bi-clipboard-data mb-2" style="font-size: 2rem; display: block;"></i>
                    Belum ada data evaluasi untuk tanggal ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Export Button -->
        <div class="export-section">
          <button class="btn-export" onclick="exportToExcel()">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            Export Data
          </button>
          <button class="btn-export btn-secondary" onclick="printReport()">
            <i class="bi bi-printer"></i>
            Print
          </button>
        </div>
      </div>

      <!-- No Results Message -->
      <div class="no-results" id="noResults" style="display: none">
        <i class="bi bi-search"></i>
        <h5>Tidak ada data ditemukan</h5>
        <p>Coba gunakan kata kunci lain</p>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      class="modal fade"
      id="detailModal"
      tabindex="-1"
      aria-labelledby="detailModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">Perkembangan</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="detail-content">
              <!-- Student Photo -->
              <div class="student-photo-section">
                <div class="student-photo-modal">
                  <img id="modalPhoto" src="{{ asset('assets/img/default-avatar.png') }}" alt="Foto Siswa" />
                </div>
                <h5 id="modalStudentName" class="student-name-modal">-</h5>
              </div>

              <!-- Progress Details -->
              <div class="progress-details">
                <div class="detail-item">
                  <div class="detail-label">
                    <i class="bi bi-book"></i>
                    Tilawati
                  </div>
                  <div class="detail-value" id="modalTilawati">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">
                    <i class="bi bi-star"></i>
                    Kemampuan
                  </div>
                  <div class="detail-value" id="modalKemampuan">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">
                    <i class="bi bi-journal-text"></i>
                    Hafalan
                  </div>
                  <div class="detail-value" id="modalHafalan">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">
                    <i class="bi bi-emoji-smile"></i>
                    Tata Krama
                  </div>
                  <div class="detail-value" id="modalTataKrama">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">
                    <i class="bi bi-chat-square-text"></i>
                    Catatan
                  </div>
                  <div class="detail-value" id="modalCatatan">-</div>
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
              onclick="printStudentReport()"
            >
              <i class="bi bi-printer"></i> Print Laporan
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar Modal -->
    <div
      class="modal fade"
      id="calendarModal"
      tabindex="-1"
      aria-labelledby="calendarModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="calendarModalLabel">Pilih Tanggal</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="calendar-container">
              <div class="calendar-header">
                <button class="btn-nav" id="prevMonth">
                  <i class="bi bi-chevron-left"></i>
                </button>
                <h5 id="calendarMonth">Oktober 2025</h5>
                <button class="btn-nav" id="nextMonth">
                  <i class="bi bi-chevron-right"></i>
                </button>
              </div>
              <div class="calendar-grid" id="calendarGrid">
                <!-- Calendar will be populated by JavaScript -->
              </div>
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

    <!-- Pass data ke JavaScript -->
    <script>
      window.evaluasiData = {
        evaluasiList: @json($evaluasiList),
        countMelanjutkan: {{ $countMelanjutkan }},
        countMengulangi: {{ $countMengulangi }},
        selectedDate: "{{ $selectedDate }}"
      };
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="{{ asset('assets/js/guru/laporan_evaluasi.js') }}"></script>
  </body>
</html>