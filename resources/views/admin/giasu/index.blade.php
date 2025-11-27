@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    {{-- TIÊU ĐỀ THAY ĐỔI THEO TRANG --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid {{ isset($isPending) ? 'fa-user-check' : 'fa-chalkboard-user' }} me-2"></i> 
            {{ isset($isPending) ? 'Duyệt Hồ Sơ Gia Sư' : 'Quản lý Gia sư' }}
        </h3>
        
        {{-- Nút thêm mới chỉ hiện ở trang Quản lý --}}
        @if(!isset($isPending))
        <a href="{{ route('admin.giasu.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Thêm Gia sư
        </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            {{-- FORM TÌM KIẾM: Action thay đổi tùy theo trang hiện tại --}}
            <form method="GET" action="{{ isset($isPending) ? route('admin.giasu.pending') : route('admin.giasu.index') }}">
                <div class="row g-3">
                    <div class="col-md-7">
                        <input type="text" name="search" class="form-control form-control-dark" 
                            placeholder="Tìm kiếm theo email, SĐT, tên gia sư..." 
                            value="{{ request('search') }}">
                    </div>
                    
                    {{-- FORM LỌC --}}
                    <div class="col-md-3">
                        <select name="trangthai" class="form-select form-select-dark">
                            {{-- value="" tức là "Tất cả" (không gửi filter) --}}
                            <option value="" {{ request('trangthai') === null || request('trangthai') === '' ? 'selected' : '' }}>Tất cả Trạng thái</option>
                            <option value="1" {{ request('trangthai') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="2" {{ request('trangthai') === '2' ? 'selected' : '' }}>Bị khóa</option>
                            <option value="0" {{ request('trangthai') === '0' ? 'selected' : '' }}>Chờ duyệt</option>
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
                            <th>Thông tin Gia sư</th>
                            <th>Liên hệ</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($giasuList as $tk)
                        <tr>
                            <td>#{{ $tk->TaiKhoanID }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- Hiển thị ảnh đại diện nếu có --}}
                                    @if(isset($tk->giasu->AnhDaiDien))
                                        <img src="{{ $tk->giasu->AnhDaiDien }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-white">{{ $tk->giasu->HoTen ?? 'Chưa cập nhật tên' }}</div>
                                        <small class="text-white-50">Ngày tạo: {{ \Carbon\Carbon::parse($tk->NgayTao)->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </td> 
                            <td>
                                <div class="d-flex flex-column">
                                    <span><i class="fa-solid fa-envelope me-2 text-white-50"></i>{{ $tk->Email }}</span>
                                    <small class="text-white-50"><i class="fa-solid fa-phone me-2 text-white-50"></i>{{ $tk->SoDienThoai ?? '---' }}</small>
                                </div>
                            </td>
                            <td>
                                {{-- LOGIC TRẠNG THÁI (QUAN TRỌNG) --}}
                                @if(isset($isPending) && $isPending)
                                    {{-- 1. Trang DUYỆT: Luôn hiển thị trạng thái hồ sơ --}}
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        <i class="fa-regular fa-clock me-1"></i> Chờ duyệt hồ sơ
                                    </span>
                                @else
                                    {{-- 2. Trang QUẢN LÝ: Hiển thị trạng thái Tài khoản --}}
                                    @if($tk->TrangThai == 1)
                                        <span class="badge rounded-pill bg-success">
                                            <i class="fa-solid fa-check me-1"></i> Hoạt động
                                        </span>
                                    @elseif($tk->TrangThai == 2)
                                        <span class="badge rounded-pill bg-danger">
                                            <i class="fa-solid fa-lock me-1"></i> Bị khóa
                                        </span>
                                    @elseif($tk->TrangThai == 0)
                                        <span class="badge rounded-pill bg-secondary">
                                            <i class="fa-regular fa-clock me-1"></i> Chờ duyệt
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end">
                                {{-- THAO TÁC: Trang Duyệt (Pending) --}}
                                @if(isset($isPending) && $isPending)
                                    <a href="{{ route('admin.giasu.show', $tk->TaiKhoanID) }}" class="btn btn-sm btn-info me-2" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i> Xem hồ sơ
                                    </a>

                                    <form action="{{ route('admin.giasu.approve', $tk->TaiKhoanID) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Xác nhận duyệt gia sư này?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fa-solid fa-check"></i> Duyệt ngay
                                        </button>
                                    </form>
                                
                                {{-- THAO TÁC: Trang Quản lý --}}
                                @else
                                    <a href="{{ route('admin.giasu.edit', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="{{ route('admin.giasu.show', $tk->TaiKhoanID) }}" class="btn btn-sm btn-outline-info" title="Xem">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.giasu.destroy', $tk->TaiKhoanID) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-white-50">
                                <i class="fa-solid fa-inbox fa-3x mb-3"></i><br>
                                {{ isset($isPending) ? 'Không có hồ sơ nào đang chờ duyệt.' : 'Không tìm thấy gia sư nào.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($giasuList->hasPages())
        <div class="card-footer bg-transparent border-top border-secondary">
            {{ $giasuList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection