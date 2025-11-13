@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Giao dịch #{{ $giaodich->GiaoDichID }}
        </h3>
        <div>
            <a href="{{ route('admin.giaodich.edit', $giaodich->GiaoDichID) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen me-2"></i> Cập nhật
            </a>
            <a href="{{ route('admin.giaodich.index') }}" class="btn btn-outline-secondary">
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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-receipt me-2"></i>Thông tin Giao dịch</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Mã giao dịch</label>
                            <p class="form-control-plaintext form-control-dark">
                                <code class="text-info">{{ $giaodich->MaGiaoDich ?? 'Chưa có' }}</code>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Loại giao dịch</label>
                            <p class="form-control-plaintext form-control-dark">
                                @php
                                    $typeColors = [
                                        'NapTien' => 'bg-success',
                                        'RutTien' => 'bg-warning',
                                        'ThanhToan' => 'bg-info'
                                    ];
                                @endphp
                                <span class="badge {{ $typeColors[$giaodich->LoaiGiaoDich] ?? 'bg-secondary' }}">
                                    {{ $giaodich->LoaiGiaoDich }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Số tiền</label>
                            <p class="form-control-plaintext form-control-dark">
                                <span class="fs-4 fw-bold text-success">{{ number_format($giaodich->SoTien, 0, ',', '.') }} đ</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trạng thái</label>
                            <p class="form-control-plaintext form-control-dark">
                                @php
                                    $statusColors = [
                                        'ChoXuLy' => 'bg-warning text-dark',
                                        'ThanhCong' => 'bg-success',
                                        'ThatBai' => 'bg-danger',
                                        'HoanTien' => 'bg-info'
                                    ];
                                    $statusText = [
                                        'ChoXuLy' => 'Chờ xử lý',
                                        'ThanhCong' => 'Thành công',
                                        'ThatBai' => 'Thất bại',
                                        'HoanTien' => 'Hoàn tiền'
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$giaodich->TrangThai] ?? 'bg-secondary' }} fs-6">
                                    {{ $statusText[$giaodich->TrangThai] ?? $giaodich->TrangThai }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Thời gian</label>
                            <p class="form-control-plaintext form-control-dark">
                                @if($giaodich->ThoiGian)
                                    <i class="fa-solid fa-clock me-2"></i>{{ \Carbon\Carbon::parse($giaodich->ThoiGian)->format('d/m/Y H:i:s') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Ghi chú</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->GhiChu ?? 'Không có' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($giaodich->lop)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>Thông tin Lớp học liên quan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Mã lớp</label>
                            <p class="form-control-plaintext form-control-dark">#{{ $giaodich->LopYeuCauID }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Môn học</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->lop->monHoc->TenMon ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Người học</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->lop->nguoiHoc->HoTen ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Gia sư</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->lop->giaSu->HoTen ?? 'Chưa có' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Tài khoản</h5>
                </div>
                <div class="card-body">
                    @if($giaodich->taiKhoan)
                        <div class="mb-3">
                            <label class="form-label text-muted">ID Tài khoản</label>
                            <p class="form-control-plaintext form-control-dark">#{{ $giaodich->TaiKhoanID }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->taiKhoan->Email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Số điện thoại</label>
                            <p class="form-control-plaintext form-control-dark">{{ $giaodich->taiKhoan->SoDienThoai ?? 'N/A' }}</p>
                        </div>
                        @if($giaodich->taiKhoan->giasu)
                            <div class="mb-3">
                                <label class="form-label text-muted">Vai trò</label>
                                <p class="form-control-plaintext form-control-dark">
                                    <span class="badge bg-primary">Gia sư</span>
                                </p>
                            </div>
                        @elseif($giaodich->taiKhoan->nguoihoc)
                            <div class="mb-3">
                                <label class="form-label text-muted">Vai trò</label>
                                <p class="form-control-plaintext form-control-dark">
                                    <span class="badge bg-info">Người học</span>
                                </p>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Không có thông tin tài khoản</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
