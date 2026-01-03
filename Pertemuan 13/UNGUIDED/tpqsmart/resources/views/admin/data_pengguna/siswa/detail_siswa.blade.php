<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Siswa - {{ $siswa->nama_lengkap }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="{{ asset('assets/css/admin/dashboard_admin.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/admin/data_pengguna/siswa/detail_siswa.css') }}" rel="stylesheet">
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
    <!-- ✅ Ganti container-fluid jadi container-full -->
    <div class="container-full">
      
      <!-- Header Card dengan Back Button -->
      <div class="header-card">
        <a href="{{ route('admin.data_pengguna') }}" class="btn-back">
          <i class="bi bi-chevron-left"></i>
        </a>
        <h4 class="header-title">Detail Siswa</h4>
      </div>

      <!-- Detail Container -->
      <div class="detail-container">
        <!-- Profile Card -->
        <div class="profile-card">
          <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&background=ffffff&color=22c55e&size=200&bold=true' }}" 
               alt="{{ $siswa->nama_lengkap }}" 
               class="profile-avatar"
               onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=ffffff&color=22c55e&size=200&bold=true';">
          <div class="profile-info">
            <h5 class="profile-name">{{ $siswa->nama_lengkap }}</h5>
            <p class="profile-id">{{ $siswa->nis }} / Kelas {{ $siswa->kelas }}</p>
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
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" value="{{ $siswa->nama_lengkap }}" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">ID Siswa (NIS)</label>
              <input type="text" class="form-control" value="{{ $siswa->nis }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kelas</label>
              <input type="text" class="form-control" value="Kelas {{ $siswa->kelas }}" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Kelamin</label>
              <input type="text" class="form-control" value="{{ $siswa->jenis_kelamin }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" class="form-control" value="{{ $siswa->tempat_lahir }}" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Tanggal Lahir</label>
              <input type="text" class="form-control" 
                     value="{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->isoFormat('D MMMM YYYY') }}" 
                     disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Umur</label>
              <input type="text" class="form-control" 
                     value="{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->age }} Tahun" 
                     disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label">Alamat Lengkap</label>
              <textarea class="form-control" rows="2" disabled>{{ $siswa->alamat }}</textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nomor HP Orang Tua</label>
              <input type="text" class="form-control" value="{{ $siswa->no_hp }}" disabled>
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
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="{{ $siswa->user->username }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" value="••••••••" disabled>
            </div>
          </div>

          <div class="info-badge">
            <i class="bi bi-info-circle-fill"></i>
            <span>Password terenkripsi untuk keamanan. Gunakan fitur edit untuk mengubah password.</span>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <a href="{{ route('admin.data_pengguna') }}" class="btn btn-outline-secondary">
              </i> Kembali
            </a>
            <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-primary">
              <i class="bi bi-pencil-square"></i> Edit Data Siswa
            </a>
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
  <script>
     $(document).ready(function() {
      // Setup CSRF token
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // Photo Upload Preview
      $('#photoUpload').on('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
          // Validate file type
          if (!file.type.match('image.*')) {
            alert('File harus berupa gambar (JPG, PNG, atau GIF)');
            $(this).val('');
            return;
          }

          // Validate file size (max 2MB)
          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            $(this).val('');
            return;
          }

          // Preview image
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#previewImage').attr('src', e.target.result);
          };
          reader.readAsDataURL(file);
        }
      });

      // Live update display nama dan NIS
      $('input[name="nama"]').on('input', function() {
        $('#displayName').text($(this).val() || '{{ $siswa->nama_lengkap }}');
      });

      $('input[name="idSiswa"], select[name="kelas"]').on('change', function() {
        const nis = $('input[name="idSiswa"]').val();
        const kelas = $('select[name="kelas"]').val();
        $('#displayNisKelas').text(nis + ' / Kelas ' + kelas);
      });

      // Form Validation
      $('input[name="no_hp"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
      });

      $('input[name="idSiswa"]').on('input', function() {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
      });

      $('input[name="username"]').on('input', function() {
        this.value = this.value.toLowerCase().replace(/\s/g, '');
      });

      // Form Submission
      $('#editSiswaForm').on'submit', function(e) {
        e.preventDefault();

        // Validate
        if (!this.checkValidity()) {
          e.stopPropagation();
          $(this).addClass('was-validated');
          return;
        }

        const username = $('input[name="username"]').val();
        const password = $('input[name="password"]').val();

        if (username.length < 5) {
          alert('Username minimal 5 karakter!');
          $('input[name="username"]').focus();
          return;
        }

        if (password && password.length < 8) {
          alert('Password minimal 8 karakter!');
          $('input[name="password"]').focus();
          return;
        }

        // Show loading
        const submitBtn = $('#btnSubmit');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).addClass('btn-loading').html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

        // Prepare FormData
        const formData = new FormData(this);

        // AJAX Submit
            $.ajax({
            url: "{{ route('admin.siswa.show', $siswa->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    showSuccessModal();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).removeClass('btn-loading').html(originalText);
                let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                alert(errorMessage);
            }
            });

            // Show success modal
            showSuccessModal();
          },
          error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            
            submitBtn.prop('disabled', false).removeClass('btn-loading').html(originalText);
            
            let errorMessage = 'Gagal menyimpan perubahan!';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
              const errors = xhr.responseJSON.errors;
              errorMessage = Object.values(errors).flat().join('\n');
            }
            
            alert(errorMessage);
          }
        });

      // Success Modal & Redirect
      function showSuccessModal() {
        // Kita bisa pakai Alert sederhana dulu atau membuat Modal HTML dinamis
        const successHtml = `
          <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content" style="border-radius: 20px; border: none; padding: 20px;">
                <div class="modal-body text-center">
                  <div class="mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #22c55e;"></i>
                  </div>
                  <h4 class="fw-bold">Berhasil!</h4>
                  <p class="text-muted">Data siswa telah diperbarui.</p>
                  <button type="button" class="btn btn-primary w-100" id="btnOkSuccess">OK</button>
                </div>
              </div>
            </div>
          </div>`;

        $('body').append(successHtml);
        const myModal = new bootstrap.Modal(document.getElementById('successModal'));
        myModal.show();

        $('#btnOkSuccess').on('click', function() {
          window.location.href = "{{ route('admin.siswa.show', $siswa->id) }}";
        });
      }

      // Logout Logic
      $('#btnLogout').on('click', function(e) {
        e.preventDefault();
        $('#logoutOverlay').addClass('show');
      });

      $('#cancelLogout').on('click', function() {
        $('#logoutOverlay').removeClass('show');
      });
  </script>
</body>
</html>