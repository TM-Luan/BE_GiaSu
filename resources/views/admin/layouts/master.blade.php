<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 1. Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- 2. Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- 3. Google Fonts (Poppins) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- 4. Custom CSS cho Dark Mode (Theme) -->
  <style>
    :root {
      --sidebar-width: 250px;
      --sidebar-bg: #1f2937; /* Xanh than đậm */
      --main-bg: #111827;     /* Nền chính đậm hơn */
      --card-bg: #1f2937;     /* Nền thẻ giống sidebar */
      --text-color: #e0e0e0;
      --text-muted: #9ca3af;
      --border-color: #374151;  /* Viền xám đậm */
      --accent-blue: #3b82f6; /* Xanh dương làm điểm nhấn */
      --accent-green: #10b981; /* Xanh lá */
    }
    body { 
        background-color: var(--main-bg); 
        color: var(--text-color); 
        font-family: 'Poppins', sans-serif;
    }
    .sidebar { 
        width: var(--sidebar-width); 
        background-color: var(--sidebar-bg); 
        height: 100vh; 
        position: fixed; 
        padding-top: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.5);
    }
    .sidebar .admin-profile {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1rem;
    }
    .sidebar .admin-profile i {
        font-size: 2.5rem;
    }
    .sidebar .nav-link {
        color: var(--text-muted);
        padding: 12px 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .sidebar .nav-link:hover {
        background-color: #374151;
        color: #fff;
    }
    .sidebar .nav-link.active {
        background-color: var(--accent-blue);
        color: #fff !important;
        border-radius: 8px;
        margin: 0 10px;
    }
    .sidebar .nav-item {
        margin-bottom: 4px; 
    }
    .sidebar .nav-link .fa-fw {
        width: 1.25em; 
    }
    .sidebar .logout-btn {
        position: absolute;
        bottom: 20px;
        width: calc(100% - 40px);
        margin: 0 20px;
    }
    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 20px; 
    }
    .card { 
        background-color: var(--card-bg); 
        border: 1px solid var(--border-color); 
        color: var(--text-color); 
        border-radius: 12px; 
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-outline-secondary {
        color: var(--text-muted);
        border-color: var(--border-color);
    }
    .btn-outline-secondary:hover {
        background-color: var(--border-color);
        color: #fff;
    }

    /* CSS cho Form và Phân trang (ĐÃ ĐẶT ĐÚNG VỊ TRÍ) */
    .form-control-dark, .form-select-dark {
        background-color: #374151;
        color: #fff;
        border-color: #4b5563;
    }
    .form-control-dark:focus, .form-select-dark:focus {
        background-color: #374151;
        color: #fff;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }
    .form-control-dark::placeholder { color: #9ca3af; }
    .form-select-dark option { background-color: #374151; }

    .pagination {
        --bs-pagination-color: var(--text-color);
        --bs-pagination-bg: var(--card-bg);
        --bs-pagination-border-color: var(--border-color);
        --bs-pagination-hover-color: #fff;
        --bs-pagination-hover-bg: var(--accent-blue);
        --bs-pagination-hover-border-color: var(--accent-blue);
        --bs-pagination-active-color: #fff;
        --bs-pagination-active-bg: var(--accent-blue);
        --bs-pagination-active-border-color: var(--accent-blue);
        --bs-pagination-disabled-color: var(--text-muted);
        --bs-pagination-disabled-bg: var(--card-bg);
        --bs-pagination-disabled-border-color: var(--border-color);
    }
  </style>

@stack('styles')
</head>
<body>
<div class="d-flex">
    <div class="sidebar">
        <div class="admin-profile d-flex align-items-center">
            <i class="fa-solid fa-circle-user text-white me-3"></i>
            <div>
                <h6 class="mb-0 text-white">{{ Auth::user()->HoTen ?? 'Admin Name' }}</h6>
                <p class="small text-muted mb-0">{{ Auth::user()->Email ?? 'admin@email.com' }}</p>
            </div>
        </div>

        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-chart-line fa-fw me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.giasu.*') ? 'active' : '' }}" href="{{ route('admin.giasu.index') }}">
                    <i class="fa-solid fa-chalkboard-user fa-fw me-2"></i>Quản lý Gia sư
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.nguoihoc.*') ? 'active' : '' }}" href="{{ route('admin.nguoihoc.index') }}">
                    <i class="fa-solid fa-user-graduate fa-fw me-2"></i>Quản lý Người học
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-book fa-fw me-2"></i>Quản lý Khóa học
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-credit-card fa-fw me-2"></i>Quản lý Giao dịch
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-triangle-exclamation fa-fw me-2"></i>Khiếu nại
                </a>
            </li>
        </ul>
        
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-btn">
            @csrf
            <button class="btn btn-danger w-100">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
            </button>
        </form>
    </div>
    
    <div class="main-content flex-grow-1">
         @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@stack('scripts')
</body>
</html>