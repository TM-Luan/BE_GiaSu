@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-book me-2"></i> Quản lý Khóa học
        </h3>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.lophoc.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control form-control-dark" 
                               placeholder="Tìm kiếm theo tên người học, gia sư, môn học..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="trangthai" class="form-select form-select-dark">
                            <option value="">Tất cả Trạng thái</option>
                            <option value="DangHoc" {{ request('trangthai') == 'DangHoc' ? 'selected' : '' }}>Đang học</option>
                            <option value="TimGiaSu" {{ request('trangthai') == 'TimGiaSu' ? 'selected' : '' }}>Tìm gia sư</option>
                            <option value="ChoDuyet" {{ request('trangthai') == 'ChoDuyet' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="HoanThanh" {{ request('trangthai') == 'HoanThanh' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="DaHuy" {{ request('trangthai') == 'DaHuy' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="hinhthuc" class="form-select form-select-dark">
                            <option value="">Tất cả Hình thức</option>
                            <option value="Online" {{ request('hinhthuc') == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Offline" {{ request('hinhthuc') == 'Offline' ? 'selected' : '' }}>Offline</option>
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
                            <th>Người học</th>
                            <th>Gia sư</th>
                            <th>Môn học</th>
                            <th>Hình thức</th>
                            <th>Học phí (đ/buổi)</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lophoc as $lh)
                        <tr>
                            <td>{{ $lh->LopYeuCauID }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $lh->nguoiHoc->HoTen ?? 'N/A' }}</span>
                                    <small class="text-white-50">{{ $lh->nguoiHoc->taiKhoan->Email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $lh->giaSu->HoTen ?? 'Chưa có' }}</span>
                                    <small class="text-white-50">{{ $lh->giaSu->taiKhoan->Email ?? '' }}</small>
                                </div>
                            </td>
                            <td>
                                {{ $lh->monHoc->TenMon ?? 'N/A' }}
                                @if($lh->khoiLop && $lh->khoiLop->BacHoc)
                                    - {{ $lh->khoiLop->BacHoc }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $lh->HinhThuc }}</span>
                            </td>
                            <td>{{ number_format($lh->HocPhi, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'DangHoc' => 'bg-success',
                                        'TimGiaSu' => 'bg-warning text-dark',
                                        'ChoDuyet' => 'bg-info',
                                        'HoanThanh' => 'bg-secondary',
                                        'DaHuy' => 'bg-danger'
                                    ];
                                    $statusText = [
                                        'DangHoc' => 'Đang học',
                                        'TimGiaSu' => 'Tìm gia sư',
                                        'ChoDuyet' => 'Chờ duyệt',
                                        'HoanThanh' => 'Hoàn thành',
                                        'DaHuy' => 'Đã hủy'
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$lh->TrangThai] ?? 'bg-secondary' }}">
                                    {{ $statusText[$lh->TrangThai] ?? $lh->TrangThai }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.lophoc.show', $lh->LopYeuCauID) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.lophoc.edit', $lh->LopYeuCauID) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.lophoc.destroy', $lh->LopYeuCauID) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp học này?');">
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
                            <td colspan="8" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-white-50 mb-3 d-block"></i>
                                <p class="text-white-50 mb-0">Không tìm thấy lớp học nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $lophoc->appends(request()->query())->links() }}
    </div>
</div>
@endsection
