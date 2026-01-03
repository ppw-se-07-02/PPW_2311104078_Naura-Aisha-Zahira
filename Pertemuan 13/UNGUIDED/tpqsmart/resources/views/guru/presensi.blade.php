<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi - TPQ Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link href="{{ asset('assets/css/guru/presensi.css') }}" rel="stylesheet" />
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
      <div class="presensi-header">
        <div class="header-left">
          <button
            class="btn-back"
            onclick="window.location.href='{{ route('guru.dashboard') }}'">
            <i class="bi bi-chevron-left"></i>
          </button>
          <h1 class="header-title">Presensi - Kelas {{ $guru->kelas }}</h1>
        </div>
        <div class="date-badge">
          <i class="bi bi-calendar3"></i>
          <span id="currentDate">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
        </div>
      </div>

      <!-- Presensi Card -->
      <div class="presensi-card">
        <h2 class="card-title">Daftar Siswa ({{ $totalSiswa }} Siswa)</h2>

        <!-- Summary Stats -->
        <div class="summary-stats">
          <div class="stat-box hadir">
            <div class="stat-label">Hadir</div>
            <div class="stat-value" id="countHadir">{{ $stats['hadir'] }}</div>
          </div>
          <div class="stat-box izin">
            <div class="stat-label">Izin</div>
            <div class="stat-value" id="countIzin">{{ $stats['izin'] }}</div>
          </div>
          <div class="stat-box sakit">
            <div class="stat-label">Sakit</div>
            <div class="stat-value" id="countSakit">{{ $stats['sakit'] }}</div>
          </div>
          <div class="stat-box alfa">
            <div class="stat-label">Alpha</div>
            <div class="stat-value" id="countAlfa">{{ $stats['alpha'] }}</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="presensi-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpha</th>
              </tr>
            </thead>
            <tbody id="studentList">
              @if($siswaList->count() > 0)
                @foreach($presensiData as $index => $data)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data['nama'] }}</td>
                    <td>
                      <div class="radio-wrapper">
                        <input type="radio" 
                               name="attendance_{{ $data['siswa_id'] }}" 
                               value="hadir" 
                               data-siswa-id="{{ $data['siswa_id'] }}"
                               {{ $data['status'] == 'hadir' ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="radio-wrapper">
                        <input type="radio" 
                               name="attendance_{{ $data['siswa_id'] }}" 
                               value="izin" 
                               data-siswa-id="{{ $data['siswa_id'] }}"
                               {{ $data['status'] == 'izin' ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="radio-wrapper">
                        <input type="radio" 
                               name="attendance_{{ $data['siswa_id'] }}" 
                               value="sakit" 
                               data-siswa-id="{{ $data['siswa_id'] }}"
                               {{ $data['status'] == 'sakit' ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="radio-wrapper">
                        <input type="radio" 
                               name="attendance_{{ $data['siswa_id'] }}" 
                               value="alpha" 
                               data-siswa-id="{{ $data['siswa_id'] }}"
                               {{ $data['status'] == 'alpha' ? 'checked' : '' }}>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    <i class="bi bi-people mb-2" style="font-size: 2rem; display: block;"></i>
                    Belum ada siswa di kelas ini.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>

        @if($siswaList->count() > 0)
          <div class="checkbox-all">
            <input type="checkbox" id="checkAll" />
            <label for="checkAll">Tandai semua hadir</label>
          </div>

          <button class="btn-submit" id="btnSubmit">
            <i class="bi bi-check-circle"></i> Simpan Presensi
          </button>
        @endif

        <!-- Decorative Elements -->
        <svg
          class="decoration decoration-left"
          viewBox="0 0 200 200"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill="#2EAF7D"
            d="M45.7,-76.2C58.7,-69.3,68.5,-56.1,75.8,-41.5C83.1,-26.9,87.9,-10.8,87.5,5.5C87.1,21.8,81.5,38.3,72.1,51.5C62.7,64.7,49.5,74.6,34.8,80.2C20.1,85.8,3.9,87.1,-11.8,84.9C-27.5,82.7,-42.7,77,-55.4,67.8C-68.1,58.6,-78.3,45.9,-83.8,31.1C-89.3,16.3,-90.1,-0.6,-85.9,-15.9C-81.7,-31.2,-72.5,-44.9,-60.6,-52.3C-48.7,-59.7,-34.1,-60.8,-20.3,-67.1C-6.5,-73.4,6.5,-84.9,19.8,-87.4C33.1,-89.9,32.7,-83.1,45.7,-76.2Z"
            transform="translate(100 100)"
          />
        </svg>
        <svg
          class="decoration decoration-right"
          viewBox="0 0 200 200"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill="#83D1A7"
            d="M41.3,-71.3C52.9,-64.3,61.3,-52.1,67.8,-38.7C74.3,-25.3,78.9,-10.7,79.2,4.2C79.5,19.1,75.5,34.3,67.8,46.8C60.1,59.3,48.7,69.1,35.5,75.5C22.3,81.9,7.3,84.9,-7.8,84.3C-22.9,83.7,-38.1,79.5,-50.8,71.5C-63.5,63.5,-73.7,51.7,-79.9,38C-86.1,24.3,-88.3,8.7,-85.5,-5.7C-82.7,-20.1,-75,-33.3,-65.4,-44.3C-55.8,-55.3,-44.3,-64.1,-31.6,-70.4C-18.9,-76.7,-5.1,-80.5,7.8,-79.8C20.7,-79.1,29.7,-78.3,41.3,-71.3Z"
            transform="translate(100 100)"
          />
        </svg>
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

    <!-- ✅ Pass data ke JavaScript -->
    <script>
      window.selectedDate = "{{ $selectedDate }}";
      window.totalSiswa = {{ $totalSiswa }};
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/guru/presensi.js') }}"></script>
  </body>
</html>