<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - TPQ Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
  </head>
  <body class="login-page">
    <div class="container-fluid">
      <div class="row min-vh-100">
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-light position-relative">
          <div class="illustration-container">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
            <div class="circle circle-4"></div>

            <img src="{{ asset('assets/img/kids.png') }}" alt="Ilustrasi Muslim" class="illustration-image" />
          </div>
        </div>

        <div class="col-lg-6 d-flex align-items-center justify-content-center login-section">
          <div class="login-container">
            <div class="logo-container text-center mb-4">
              <div class="logo-icon">
                <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="TPQ Logo" class="logo-img" />
              </div>
              <div class="stars">
                <span class="star">✦</span>
                <span class="star">✦</span>
                <span class="star">✦</span>
              </div>
            </div>

            <h2 class="text-center text-white mb-2">Assalamu'alaikum...</h2>
            <p class="text-center text-white-50 mb-4">Silahkan masuk ke akun anda</p>

            @if(session('error'))
                <div class="alert alert-danger py-2" style="border-radius: 10px;">
                    {{ session('error') }}
                </div>
            @endif
            
            <form id="loginForm" method="POST" action="{{ route('login') }}">
              @csrf <div class="mb-3">
                <label for="username" class="form-label text-white">Username</label>
                <input
                  type="text"
                  name="username" 
                  class="form-control form-control-transparent @error('username') is-invalid @enderror"
                  id="username"
                  value="{{ old('username') }}"
                  placeholder="Masukkan username"
                  required
                />
                @error('username')
                    <div class="small text-warning mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-2">
                <label for="password" class="form-label text-white">Kata Sandi</label>
                <div class="password-input-group">
                  <input
                    type="password"
                    name="password"
                    class="form-control form-control-transparent"
                    id="password"
                    placeholder="Masukkan kata sandi"
                    required
                  />
                  <button type="button" class="btn-toggle-password" id="togglePassword">
                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    <span class="ms-2 text-white-50">Hide</span>
                  </button>
                </div>
              </div>

              <div class="text-end mb-4">
                <a href="#" class="text-white-50 text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                    Lupa Kata Sandi?
                </a>             
            </div>

              <button type="submit" class="btn btn-login w-100">Masuk</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-body text-center p-5">
                    <h3 class="mb-4" style="color: #4A4A4A; font-weight: 700;">Lupa Kata Sandi?</h3>
                    <p class="mb-4" style="color: #666;">
                    Jika Anda lupa kata sandi, silakan hubungi admin <strong>TPQ Smart</strong> melalui WhatsApp untuk proses reset akun.
                    </p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn" style="border: 2px solid #25D366; color: #25D366; border-radius: 50px; padding: 10px 25px; font-weight: 600;">
                    <i class="bi bi-whatsapp"></i> Hubungi Admin
                    </a>
                    <div class="mt-4">
                    <button type="button" class="btn text-muted small" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="{{ asset('assets/js/script.js') }}"></script>
  </body>
</html>