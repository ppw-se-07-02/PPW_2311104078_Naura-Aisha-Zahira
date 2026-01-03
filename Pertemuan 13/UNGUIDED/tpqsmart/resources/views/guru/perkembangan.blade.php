<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perkembangan - TPQ Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link href="{{ asset('assets/css/guru/perkembangan.css') }}" rel="stylesheet" />
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
      <div class="perkembangan-header">
        <div class="header-left">
          <button
            class="btn-back"
            onclick="window.location.href='{{ route('guru.dashboard') }}'">
            <i class="bi bi-chevron-left"></i>
          </button>
          <h1 class="header-title">Perkembangan - Kelas {{ $guru->kelas }}</h1>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-left">
          <div class="dropdown">
            <button
              class="btn-filter dropdown-toggle"
              type="button"
              id="filterDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <span id="filterText">Semua ({{ $totalSiswa }})</span>
              <i class="bi bi-chevron-down"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
              <li>
                <a class="dropdown-item" href="#" data-filter="semua">Semua ({{ $totalSiswa }})</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" data-filter="sudah"
                  >Sudah dicatat ({{ $sudahDicatat }})</a
                >
              </li>
              <li>
                <a class="dropdown-item" href="#" data-filter="belum"
                  >Belum dicatat ({{ $belumDicatat }})</a
                >
              </li>
            </ul>
          </div>
        </div>
        <div class="filter-right">
          <div class="date-badge" id="currentDate" style="cursor: pointer;">
            <i class="bi bi-calendar3"></i>
            <span>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
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

      <!-- Students Table -->
      <div class="table-container">
        <div class="table-header">
          <h5>Daftar Siswa Kelas {{ $guru->kelas }}</h5>
        </div>
        <div class="table-responsive">
          <table class="perkembangan-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Catat</th>
              </tr>
            </thead>
            <tbody id="studentsList">
              @if(count($perkembanganData) > 0)
                @foreach($perkembanganData as $index => $data)
                  <tr data-status="{{ $data['status'] }}" data-nama="{{ strtolower($data['nama']) }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data['nama'] }}</td>
                    <td>
                      <span class="status-badge {{ $data['status'] === 'sudah' ? 'status-sudah' : 'status-belum' }}">
                        {{ $data['status'] === 'sudah' ? 'Sudah dicatat' : 'Belum dicatat' }}
                      </span>
                    </td>
                    <td>
                      @if($data['status'] === 'sudah')
                        <button class="btn-action btn-edit" 
                                data-siswa-id="{{ $data['siswa_id'] }}"
                                data-siswa-nama="{{ $data['nama'] }}"
                                data-siswa-nis="{{ $data['id'] }}"
                                onclick="editProgress(this)">
                          <i class="bi bi-pencil"></i> Edit
                        </button>
                      @else
                        <button class="btn-action btn-isi" 
                                data-siswa-id="{{ $data['siswa_id'] }}"
                                data-siswa-nama="{{ $data['nama'] }}"
                                data-siswa-nis="{{ $data['id'] }}"
                                onclick="inputProgress(this)">
                          <i class="bi bi-plus-circle"></i> Isi
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">
                    <i class="bi bi-people mb-2" style="font-size: 2rem; display: block;"></i>
                    Belum ada siswa di kelas ini.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

      <!-- No Results Message -->
      <div class="no-results" id="noResults" style="display: none">
        <i class="bi bi-search"></i>
        <h5>Tidak ada data ditemukan</h5>
        <p>Coba gunakan filter atau kata kunci lain</p>
      </div>
    </div>

    <!-- Progress Input Modal -->
    <div
      class="modal fade"
      id="progressModal"
      tabindex="-1"
      aria-labelledby="progressModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="progressModalLabel">
              Catat Perkembangan
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="student-info-header">
              <div class="student-name-modal" id="modalStudentName">
                -
              </div>
            </div>

            <form id="progressForm">
              <input type="hidden" id="inputSiswaId">
              
              <div class="mb-3">
                <label class="form-label">Tilawati <span class="text-danger">*</span></label>
                <select class="form-select" id="inputTilawati" required>
                  <option value="">Pilih jilid...</option>
                  <option value="Jilid 1">Jilid 1</option>
                  <option value="Jilid 2">Jilid 2</option>
                  <option value="Jilid 3">Jilid 3</option>
                  <option value="Jilid 4">Jilid 4</option>
                  <option value="Jilid 5">Jilid 5</option>
                  <option value="Jilid 6">Jilid 6</option>
                  <option value="Al-Quran">Al-Quran</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Halaman</label>
                <input
                  type="text"
                  class="form-control"
                  id="inputHalaman"
                  placeholder="Contoh: Halaman 5"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Kemampuan <span class="text-danger">*</span></label>
                <select class="form-select" id="inputKemampuan" required>
                  <option value="">Pilih kemampuan...</option>
                  <option value="Sangat Baik">Sangat Baik</option>
                  <option value="Baik">Baik</option>
                  <option value="Cukup">Cukup</option>
                  <option value="Perlu Bimbingan">Perlu Bimbingan</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Hafalan <span class="text-danger">*</span></label>
                <input
                  type="text"
                  class="form-control"
                  id="inputHafalan"
                  placeholder="Contoh: Surah Al-Fatihah"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Ayat</label>
                <input
                  type="text"
                  class="form-control"
                  id="inputAyat"
                  placeholder="Contoh: Ayat 1-7"
                />
              </div>

              <div class="mb-3">
                <label class="form-label">Tata Krama</label>
                <textarea
                  class="form-control"
                  id="inputTataKrama"
                  rows="3"
                  placeholder="Catatan perilaku dan tata krama siswa..."
                ></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea
                  class="form-control"
                  id="inputCatatan"
                  rows="3"
                  placeholder="Catatan tambahan perkembangan siswa..."
                ></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Batal
            </button>
            <button
              type="button"
              class="btn btn-primary"
              id="btnSaveProgress"
            >
              <i class="bi bi-save"></i> Simpan
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

    <!-- Pass data ke JavaScript -->
    <script>
      window.selectedDate = "{{ $selectedDate }}";
      window.totalSiswa = {{ $totalSiswa }};
      window.sudahDicatat = {{ $sudahDicatat }};
      window.belumDicatat = {{ $belumDicatat }};
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/guru/perkembangan.js') }}"></script>
  </body>
</html>