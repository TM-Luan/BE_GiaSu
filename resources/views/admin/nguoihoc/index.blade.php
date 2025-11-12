@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-user-graduate me-2"></i> Quản lý Người học
        </h3>
        <a href="{{ route('admin.nguoihoc.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Thêm Người học
        </a>
    </div>

    <!-- ============================================= -->
    <!-- KHUNG LỌC (FORM TÌM KIẾM) ĐÃ CÓ Ở ĐÂY -->
    <!-- ============================================= -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.nguoihoc.index') }}">
                <div class="row g-3">
                    <div class="col-md-7">
                        <input type="text" name="search" class="form-control form-control-dark" 
                               placeholder="Tìm kiếm theo email, SĐT, tên người học..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="trangthai" class="form-select form-select-dark">
                            <option value="">Tất cả Trạng thái</option>
                            <option value="1" {{ request('trangthai') == '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('trangthai') == '0' ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-2"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- ============================================= -->
    <!-- KẾT THÚC KHUNG LỌC -->
    <!-- ============================================= -->

    <!-- Bảng Dữ liệu -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Người học</th>
                            <th>Thông tin Liên hệ (Email/SĐT)</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nguoihocList as $tk)
                        <tr>
                            <td>{{ $tk->TaiKhoanID }}</td>
                            <td>{{ $tk->nguoihoc->HoTen ?? 'Chưa cập nhật' }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $tk->Email }}</span>
                                    <small class="text-muted">{{ $tk->SoDienThoai ?? 'Chưa có SĐT' }}</small>
                                </div>
                            </td>
                            <td>{{ $tk->NgayTao ? \Carbon\Carbon::parse($tk->NgayTao)->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $tk->TrangThai == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tk->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.nguoihoc.edit', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a> 
                                <a href="{{ route('admin.nguoihoc.show', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-info" title="Xem">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                                                {{-- Thêm nút xóa hoặc xem nếu cần --}}
                                <form action="{{ route('admin.nguoihoc.destroy', $tk->TaiKhoanID) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa người học {{ $tk->Email }}? Thao tác này không thể hoàn tác!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Không tìm thấy người học nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Phân trang -->
        @if($nguoihocList->hasPages())
        <div class="card-footer bg-transparent">
            {{ $nguoihocList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection