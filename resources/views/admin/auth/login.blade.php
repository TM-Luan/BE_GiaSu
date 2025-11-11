<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow p-4" style="width:400px;">
      <h4 class="text-center mb-3">Trang quản trị Gia Sư</h4>
      <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="Email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Mật khẩu</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        @error('Email')
          <p class="text-danger small">{{ $message }}</p>
        @enderror
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
      </form>
    </div>
  </div>
</body>
</html>
