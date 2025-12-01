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

  <!-- 4. Custom CSS Admin Theme (Dark admin theme restored) -->
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #ffffff; /* Sidebar now white */
            --main-bg: #ffffff;     /* MAIN CONTENT: white background */
            --card-bg: #ffffff;     /* Card background: white */
            --text-color: #0f172a;  /* Global default dark text */
            --sidebar-text: var(--text-accent); /* Sidebar text: use accent blue */
            --content-text: var(--text-accent); /* Main content text: use accent blue per request */
            --text-accent: #2186ff; /* Accent blue */
            --text-muted: #6b7280;  /* Muted text for light bg */
            --border-color: rgba(15,23,42,0.06);  /* Subtle border on light bg */
            --page-bg: #f8fafc; /* Page background behind sidebar/main to separate from white panels */
        }
        body {
            background-color: var(--page-bg);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            box-shadow: 0 0 0 1px rgba(15,23,42,0.02) inset;
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }
    .sidebar .admin-profile {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1rem;
    }
    .sidebar .admin-profile i {
        font-size: 2.2rem;
        color: var(--text-accent) !important;
        background-color: #ffffff;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(2,6,23,0.06);
        margin-right: 0.75rem;
    }
    .sidebar .admin-profile h6 { color: var(--sidebar-text); }
    .sidebar .admin-profile p { color: var(--text-muted); }
    .sidebar .nav-link {
        color: var(--sidebar-text);
        padding: 12px 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: all 0.15s ease;
    }
    .sidebar .nav-link:hover { background-color: rgba(33,134,255,0.06); color: var(--sidebar-text); }
    .sidebar .nav-link.active {
        background-color: var(--text-accent);
        color: #ffffff !important;
        border-radius: 8px;
        margin: 0 10px;
    }
    .sidebar .nav-item { margin-bottom: 4px; }
    .sidebar .nav-link .fa-fw { width: 1.25em; }
    .sidebar .logout-btn { margin: 20px; }

    /* Main content area (white) */
    .main-content { margin-left: var(--sidebar-width); padding: 20px; min-height: 100vh; background-color: var(--main-bg); color: var(--content-text); }
    .card { background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--content-text); border-radius: 12px; padding: 24px; }

    /* Make date/filter buttons in main content use accent blue for better contrast */
    .main-content .btn-outline-secondary { color: var(--text-accent); border-color: var(--border-color); background-color: transparent; }
    .main-content .btn-outline-secondary:hover { background-color: rgba(33,134,255,0.06); color: var(--text-accent); }

    /* Form styles for dark admin */
    .form-control-dark, .form-select-dark {
        background-color: #0b1220;
        color: var(--text-color);
        border-color: rgba(255,255,255,0.04);
    }
    .form-control-dark:focus, .form-select-dark:focus { box-shadow: 0 0 0 0.15rem rgba(96,165,250,0.08); border-color: var(--text-accent); }

    .pagination { --bs-pagination-color: var(--text-color); }

    h1,h2,h3,h4,h5,h6 { color: var(--text-accent) !important; }
    a { color: var(--text-accent); }

    /* Tables inside main content should be light and readable on white background. Do not force global overrides on badges/buttons. */
    .main-content .table { background-color: transparent; color: var(--content-text); }
    .main-content .table thead th { background-color: #ffffff; color: var(--content-text); border-bottom: 1px solid var(--border-color); }
    .main-content .table tbody tr { background-color: #ffffff; color: var(--content-text); }
    .main-content .table tbody td, .main-content .table tbody th { border-color: var(--border-color); }

    /* Make form inputs/selects in main content white with dark text */
    .main-content .form-control,
    .main-content .form-control-sm,
    .main-content .form-control-dark,
    .main-content .form-select-dark,
    .main-content .form-select,
    .main-content input[type="search"] {
        background-color: #ffffff !important;
        color: var(--content-text) !important;
        border: 1px solid var(--border-color) !important;
    }
    .main-content .form-control:focus,
    .main-content .form-select:focus,
    .main-content .form-control-dark:focus,
    .main-content .form-select-dark:focus {
        border-color: var(--text-accent) !important;
        box-shadow: 0 0 0 0.12rem rgba(33,134,255,0.08) !important;
    }

    /* Stat cards: numbers and key stats should be accent blue */
    .stat-card p { color: var(--text-muted); margin-bottom: 0.25rem; }
    .stat-card h3 { color: var(--text-accent) !important; margin-bottom: 0.25rem; }

    /* Ensure small muted text uses the content muted color */
    .main-content .text-muted { color: var(--text-muted) !important; }

    .stat-card p { color: var(--text-muted); margin-bottom: 0.25rem; }
    .stat-card h3 { color: var(--text-accent); margin-bottom: 0.25rem; }

    /* Make dropdown menus light to match white header area and ensure items are readable/interactive */
    .dropdown-menu { background-color: #ffffff; color: var(--content-text); border: 1px solid var(--border-color); }
    .dropdown-menu .dropdown-item { color: var(--content-text); }
    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus,
    .dropdown-menu .dropdown-item.active { color: var(--text-accent); background-color: rgba(33,134,255,0.04); }
    .dropdown-menu .dropdown-item.disabled, .dropdown-menu .dropdown-item[aria-disabled="true"] { color: var(--text-muted); }

    /* Map common white-classes inside main-content to readable colors on white background */
    .main-content .text-white { color: var(--content-text); }
    .main-content .text-white-50 { color: var(--text-muted) !important; }

    /* Form plaintext and label defaults */
    .main-content .form-control-plaintext { color: var(--text-color); }
    .main-content label { color: var(--text-muted); }

    /* bg-dark and text-light keep dark semantics */
    .main-content .bg-dark { background-color: #0b1220 !important; color: var(--text-color) !important; }
    .main-content .text-light { color: rgba(255,255,255,0.7) !important; }
  </style>

@stack('styles')
</head>
<body>
<div class="d-flex">
    <div class="sidebar d-flex flex-column justify-content-between">
        <div>
            <div class="admin-profile d-flex align-items-center">
                <i class="fa-solid fa-circle-user text-white me-3"></i>
                <div>
                    <h6 class="mb-0 text-white">{{ Auth::user()->HoTen ?? 'Admin Name' }}</h6>
                    <p class="small text-muted mb-0">{{ Auth::user()->Email ?? 'admin@email.com' }}</p>
                </div>
            </div>

            <ul class="nav flex-column px-2">
                {{-- 1. Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-line fa-fw me-2"></i>Dashboard
                    </a>
                </li>
                
                {{-- 2. Quản lý Gia sư (Chỉ hiện danh sách đã duyệt) --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.giasu.index') ? 'active' : '' }}" 
                       href="{{ route('admin.giasu.index') }}">
                        <i class="fa-solid fa-chalkboard-user fa-fw me-2"></i>Quản lý Gia sư
                    </a>
                </li>

                {{-- 3. Quản lý Người học --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.nguoihoc.*') ? 'active' : '' }}" href="{{ route('admin.nguoihoc.index') }}">
                        <i class="fa-solid fa-user-graduate fa-fw me-2"></i>Quản lý Người học
                    </a>
                </li>

                {{-- 4. Quản lý Khóa học --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.lophoc.*') ? 'active' : '' }}" href="{{ route('admin.lophoc.index') }}">
                        <i class="fa-solid fa-book fa-fw me-2"></i>Quản lý Khóa học
                    </a>
                </li>

                {{-- 5. Quản lý Giao dịch --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.giaodich.*') ? 'active' : '' }}" href="{{ route('admin.giaodich.index') }}">
                        <i class="fa-solid fa-credit-card fa-fw me-2"></i>Quản lý Giao dịch
                    </a>
                </li>

                {{-- 6. Khiếu nại --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.khieunai.*') ? 'active' : '' }}" href="{{ route('admin.khieunai.index') }}">
                        <i class="fa-solid fa-triangle-exclamation fa-fw me-2"></i>Khiếu nại
                    </a>
                </li>
                {{-- 7. Quản lý Đánh giá - Dòng mới thêm --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.danhgia.*') ? 'active' : '' }}" href="{{ route('admin.danhgia.index') }}">
                        <i class="fa-solid fa-star fa-fw me-2"></i>Quản lý Đánh giá
                    </a>
                </li>

                {{-- 7. DUYỆT HỒ SƠ GIA SƯ (Nằm cuối, đã xóa gạch ngang) --}}
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ Request::routeIs('admin.giasu.pending') ? 'active' : '' }}" 
                       href="{{ route('admin.giasu.pending') }}">
                        <span>
                            <i class="fa-solid fa-user-check fa-fw me-2"></i>Duyệt hồ sơ gia sư
                        </span>
                        @php
                            // Tính số lượng gia sư chờ duyệt (TrangThai = 2)
                            $countPendingGiaSu = \App\Models\GiaSu::where('TrangThai', 2)->count();
                        @endphp
                        @if($countPendingGiaSu > 0)
                            <span class="badge bg-danger rounded-pill">{{ $countPendingGiaSu }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        
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