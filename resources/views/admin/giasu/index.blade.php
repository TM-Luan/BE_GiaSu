@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-chalkboard-user me-2"></i> Quản lý Gia sư
        </h3>
        <a href="{{ route('admin.giasu.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Thêm Gia sư
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            {{-- SỬA ACTION TRỎ ĐẾN ROUTE GIA SƯ --}}
            <form method="GET" action="{{ route('admin.giasu.index') }}">
                <div class="row g-3">
                    <div class="col-md-7">
                        <input type="text" name="search" class="form-control form-control-dark" 
                               {{-- SỬA PLACEHOLDER --}}
                               placeholder="Tìm kiếm theo email, SĐT, tên gia sư..." 
                               value="{{ request('search') }}">
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
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Gia sư</th>
                            <th>Thông tin Liên hệ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($giasuList as $tk) {{-- Biến $giasuList từ controller --}}
                        <tr>
                            <td>{{ $tk->TaiKhoanID }}</td>
                            {{-- Dùng ?? để tránh lỗi nếu gia sư chưa cập nhật profile --}}
                            <td>{{ $tk->giasu->HoTen ?? 'Chưa cập nhật' }}</td> 
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $tk->Email }}</span>
                                    <small class="fw">{{ $tk->SoDienThoai ?? 'Chưa có SĐT' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $tk->TrangThai == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tk->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.giasu.edit', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="{{ route('admin.giasu.show', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-info" title="Xem">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                                                {{-- Thêm nút xóa hoặc xem nếu cần --}}
                                <form action="{{ route('admin.giasu.destroy', $tk->TaiKhoanID) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa gia sư {{ $tk->Email }}? Thao tác này không thể hoàn tác!');">
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
                            <td colspan="5" class="text-center py-4">Không tìm thấy gia sư nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($giasuList->hasPages())
        <div class="card-footer bg-transparent">
            {{ $giasuList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection