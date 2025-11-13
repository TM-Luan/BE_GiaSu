@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Lớp học #{{ $lophoc->LopYeuCauID }}
        </h3>
        <div>
            <a href="{{ route('admin.lophoc.edit', $lophoc->LopYeuCauID) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
            </a>
            <form action="{{ route('admin.lophoc.destroy', $lophoc->LopYeuCauID) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp học này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash me-2"></i> Xóa
                </button>
            </form>
            <a href="{{ route('admin.lophoc.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Thông tin Người học</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="form-control-plaintext form-control-dark mb-2">
                            <i class="fa-solid fa-user me-2 text-muted"></i>
                            <span class="text-white-50">Họ tên:</span> 
                            <strong class="text-white ms-2">{{ $lophoc->nguoiHoc->HoTen ?? 'Chưa có thông tin' }}</strong>
                        </p>
                        <p class="form-control-plaintext form-control-dark mb-2">
                            <i class="fa-solid fa-envelope me-2 text-muted"></i>
                            <span class="text-white-50">Email:</span> 
                            <span class="text-white ms-2">{{ $lophoc->nguoiHoc->taiKhoan->Email ?? 'Chưa có email' }}</span>
                        </p>
                        <p class="form-control-plaintext form-control-dark mb-0">
                            <i class="fa-solid fa-phone me-2 text-muted"></i>
                            <span class="text-white-50">Số điện thoại:</span> 
                            <span class="text-white ms-2">{{ $lophoc->nguoiHoc->taiKhoan->SoDienThoai ?? 'Chưa có số điện thoại' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-chalkboard-user me-2"></i>Thông tin Gia sư</h5>
                </div>
                <div class="card-body">
                    @if($lophoc->giaSu)
                        <div class="mb-3">
                            <p class="form-control-plaintext form-control-dark mb-2">
                                <i class="fa-solid fa-user-tie me-2 text-muted"></i>
                                <span class="text-white-50">Họ tên:</span> 
                                <strong class="text-white ms-2">{{ $lophoc->giaSu->HoTen }}</strong>
                            </p>
                            <p class="form-control-plaintext form-control-dark mb-2">
                                <i class="fa-solid fa-envelope me-2 text-muted"></i>
                                <span class="text-white-50">Email:</span> 
                                <span class="text-white ms-2">{{ $lophoc->giaSu->taiKhoan->Email ?? 'Chưa có email' }}</span>
                            </p>
                            <p class="form-control-plaintext form-control-dark mb-0">
                                <i class="fa-solid fa-phone me-2 text-muted"></i>
                                <span class="text-white-50">Số điện thoại:</span> 
                                <span class="text-white ms-2">{{ $lophoc->giaSu->taiKhoan->SoDienThoai ?? 'Chưa có số điện thoại' }}</span>
                            </p>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Chưa có gia sư nhận lớp
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>Thông tin Lớp học</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-book-open me-2 text-muted"></i>
                        <span class="text-white-50">Môn học:</span> 
                        <strong class="text-white ms-2">{{ $lophoc->monHoc->TenMon ?? 'Chưa có môn học' }}</strong>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-graduation-cap me-2 text-muted"></i>
                        <span class="text-white-50">Khối lớp:</span> 
                        <strong class="text-white ms-2">{{ $lophoc->khoiLop->BacHoc ?? 'Chưa xác định' }}</strong>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-users me-2 text-muted"></i>
                        <span class="text-white-50">Đối tượng:</span> 
                        <span class="text-white ms-2">{{ $lophoc->doiTuong->TenDoiTuong ?? 'Chưa xác định' }}</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-laptop me-2 text-muted"></i>
                        <span class="text-white-50">Hình thức:</span> 
                        <span class="badge bg-info ms-2">
                            {{ $lophoc->HinhThuc == 'Online' ? 'Online' : ($lophoc->HinhThuc == 'Offline' ? 'Offline' : $lophoc->HinhThuc) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-money-bill-wave me-2 text-muted"></i>
                        <span class="text-white-50">Học phí:</span> 
                        <span class="text-success fw-bold ms-2">{{ number_format($lophoc->HocPhi ?? 0, 0, ',', '.') }} đ/buổi</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-clock me-2 text-muted"></i>
                        <span class="text-white-50">Thời lượng:</span> 
                        <span class="text-white ms-2">{{ $lophoc->ThoiLuong ?? 0 }} phút/buổi</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-list-ol me-2 text-muted"></i>
                        <span class="text-white-50">Số buổi:</span> 
                        <span class="text-white ms-2">{{ $lophoc->SoLuong ?? 0 }} buổi</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-circle-info me-2 text-muted"></i>
                        <span class="text-white-50">Trạng thái:</span> 
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
                        <span class="badge {{ $statusColors[$lophoc->TrangThai] ?? 'bg-secondary' }} ms-2">
                            {{ $statusText[$lophoc->TrangThai] ?? $lophoc->TrangThai }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-0">
                        <i class="fa-solid fa-calendar-plus me-2 text-muted"></i>
                        <span class="text-white-50">Ngày tạo:</span> 
                        <span class="text-white ms-2">{{ $lophoc->NgayTao ? \Carbon\Carbon::parse($lophoc->NgayTao)->format('d/m/Y H:i') : 'Chưa có thông tin' }}</span>
                    </p>
                </div>
                <div class="col-md-12 mb-3">
                    <p class="form-control-plaintext form-control-dark mb-1">
                        <i class="fa-solid fa-align-left me-2 text-muted"></i>
                        <span class="text-white-50">Mô tả yêu cầu:</span>
                    </p>
                    <div class="bg-dark p-3 rounded">
                        <span class="text-white">{{ $lophoc->MoTa ?? 'Không có mô tả' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($lophoc->lichHocs && $lophoc->lichHocs->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-calendar me-2"></i>Lịch học ({{ $lophoc->lichHocs->count() }} buổi)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Ngày học</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lophoc->lichHocs->take(10) as $lh)
                        <tr>
                            <td>{{ $lh->NgayHoc ? \Carbon\Carbon::parse($lh->NgayHoc)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $lh->GioBatDau }} - {{ $lh->GioKetThuc }}</td>
                            <td><span class="badge bg-info">{{ $lh->TrangThai }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($lophoc->lichHocs->count() > 10)
                    <p class="text-muted text-center">Và {{ $lophoc->lichHocs->count() - 10 }} buổi khác...</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
