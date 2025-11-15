<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập - Gia Sư Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --main-bg: #111827;
      --card-bg: #1f2937;
      --text-color: #e0e0e0;
      --text-muted: #9ca3af;
      --border-color: #374151;
      --accent-blue: #3b82f6;
      --accent-hover: #2563eb;
      --input-bg: #374151;
    }
    
    body {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
      color: var(--text-color);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
    }
    
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .login-card {
      background: var(--card-bg);
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }
    
    .login-header {
      background: linear-gradient(135deg, var(--accent-blue) 0%, #2563eb 100%);
      padding: 2.5rem 2rem;
      text-align: center;
      position: relative;
    }
    
    .login-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="rgba(255,255,255,0.05)"/></svg>');
      background-size: cover;
      opacity: 0.3;
    }
    
    .logo-container {
      width: 80px;
      height: 80px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      position: relative;
      z-index: 1;
    }
    
    .logo-container i {
      font-size: 2.5rem;
      color: var(--accent-blue);
    }
    
    .login-header h2 {
      color: white;
      font-weight: 600;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 1;
    }
    
    .login-header p {
      color: rgba(255, 255, 255, 0.9);
      margin: 0;
      position: relative;
      z-index: 1;
    }
    
    .login-body {
      padding: 2rem;
    }
    
    .form-label {
      color: var(--text-color);
      font-weight: 500;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .form-label i {
      color: var(--accent-blue);
    }
    
    .form-control {
      background: var(--input-bg);
      border: 1px solid var(--border-color);
      color: var(--text-color);
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      background: var(--input-bg);
      border-color: var(--accent-blue);
      color: var(--text-color);
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    
    .form-control::placeholder {
      color: var(--text-muted);
    }
    
    .btn-login {
      background: var(--accent-blue);
      color: white;
      border: none;
      padding: 0.875rem 2rem;
      border-radius: 8px;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .btn-login:hover {
      background: var(--accent-hover);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .btn-login:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    .register-link {
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-color);
    }
    
    .register-link a {
      color: var(--accent-blue);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    
    .register-link a:hover {
      color: var(--accent-hover);
      text-decoration: underline;
    }
    
    .alert {
      border-radius: 8px;
      border: none;
    }
    
    .alert-danger {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
      border-left: 4px solid #ef4444;
    }
    
    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 0;
      z-index: 10;
    }
    
    .password-toggle:hover {
      color: var(--text-color);
    }
    
    .input-wrapper {
      position: relative;
    }
    
    .forgot-password {
      text-align: right;
      margin-top: 0.5rem;
    }
    
    .forgot-password a {
      color: var(--accent-blue);
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }
    
    .forgot-password a:hover {
      color: var(--accent-hover);
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <!-- Header -->
      <div class="login-header">
        <div class="logo-container">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        
        <h2>Đăng nhập Admin</h2>
        <p>Trang quản trị hệ thống Gia Sư</p>
      </div>

      <!-- Body -->
      <div class="login-body">
        @if(session('error'))
          <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            {{ session('error') }}
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border-left: 4px solid #10b981;">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
          </div>
        @endif

        @error('Email')
          <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            {{ $message }}
          </div>
        @enderror

        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
          @csrf

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-envelope"></i>
              Email
            </label>
            <input type="email" name="Email" class="form-control" placeholder="example@email.com" 
                   value="{{ old('Email') }}" required autofocus>
          </div>

          <!-- Mật khẩu -->
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-lock"></i>
              Mật khẩu
            </label>
            <div class="input-wrapper">
              <input type="password" name="password" id="password" class="form-control" 
                     placeholder="Nhập mật khẩu" required>
              <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="fa-solid fa-eye" id="password-icon"></i>
              </button>
            </div>
            <div class="forgot-password">
              <a href="#">Quên mật khẩu?</a>
            </div>
          </div>

          <!-- Nút đăng nhập -->
          <button type="submit" class="btn-login" id="submitBtn">
            <i class="fa-solid fa-right-to-bracket"></i>
            ĐĂNG NHẬP
          </button>

          <!-- Link đăng ký -->
          <div class="register-link">
            <span style="color: var(--text-muted);">Chưa có tài khoản?</span>
            <a href="{{ route('admin.register') }}">Đăng ký ngay</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle hiển thị mật khẩu
    function togglePassword() {
      const input = document.getElementById('password');
      const icon = document.getElementById('password-icon');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Disable button khi submit
    document.getElementById('loginForm').addEventListener('submit', function() {
      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
    });

    // Auto hide alerts sau 5 giây
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
          alert.remove();
        }, 500);
      });
    }, 5000);
  </script>
</body>
</html>
