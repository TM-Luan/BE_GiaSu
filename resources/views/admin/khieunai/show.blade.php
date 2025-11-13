@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Khiếu nại #{{ $khieunai->KhieuNaiID }}
        </h3>
        <div>
            <a href="{{ route('admin.khieunai.edit', $khieunai->KhieuNaiID) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen me-2"></i> Xử lý
            </a>
            <a href="{{ route('admin.khieunai.index') }}" class="btn btn-outline-secondary">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-triangle-exclamation me-2"></i>Nội dung Khiếu nại</h5>
                    @php
                        $statusColors = [
                            'TiepNhan' => 'bg-info',
                            'DangXuLy' => 'bg-warning text-dark',
                            'DaGiaiQuyet' => 'bg-success',
                            'TuChoi' => 'bg-danger'
                        ];
                        $statusText = [
                            'TiepNhan' => 'Tiếp nhận',
                            'DangXuLy' => 'Đang xử lý',
                            'DaGiaiQuyet' => 'Đã giải quyết',
                            'TuChoi' => 'Từ chối'
                        ];
                    @endphp
                    <span class="badge {{ $statusColors[$khieunai->TrangThai] ?? 'bg-secondary' }} fs-6">
                        {{ $statusText[$khieunai->TrangThai] ?? $khieunai->TrangThai }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label text-muted">Nội dung khiếu nại</label>
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <p class="mb-0 text-white" style="white-space: pre-line;">{{ $khieunai->NoiDung }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày tạo</label>
                            <p class="form-control-plaintext form-control-dark">
                                @if($khieunai->NgayTao)
                                    <i class="fa-solid fa-calendar me-2"></i>{{ $khieunai->NgayTao->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày xử lý</label>
                            <p class="form-control-plaintext form-control-dark">
                                @if($khieunai->NgayXuLy)
                                    <i class="fa-solid fa-check-circle me-2"></i>{{ $khieunai->NgayXuLy->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Chưa xử lý</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($khieunai->PhanHoi)
                    <div class="mb-3">
                        <label class="form-label text-muted">Phản hồi của Admin</label>
                        <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
                            <p class="mb-0 text-white" style="white-space: pre-line;">{{ $khieunai->PhanHoi }}</p>
                        </div>
                    </div>
                    @endif

                    @if($khieunai->GhiChu)
                    <div class="mb-3">
                        <label class="form-label text-muted">Ghi chú nội bộ</label>
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $khieunai->GhiChu }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($khieunai->lop)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>Lớp học liên quan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Mã lớp</label>
                            <p class="form-control-plaintext form-control-dark">#{{ $khieunai->LopYeuCauID }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Môn học</label>
                            <p class="form-control-plaintext form-control-dark">{{ $khieunai->lop->monHoc->TenMon ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Trạng thái lớp</label>
                            <p class="form-control-plaintext form-control-dark">
                                <span class="badge bg-info">{{ $khieunai->lop->TrangThai }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Người học</label>
                            <p class="form-control-plaintext form-control-dark">{{ $khieunai->lop->nguoiHoc->HoTen ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Gia sư</label>
                            <p class="form-control-plaintext form-control-dark">{{ $khieunai->lop->giaSu->HoTen ?? 'Chưa có' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($khieunai->giaoDich)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-credit-card me-2"></i>Giao dịch liên quan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Mã giao dịch</label>
                            <p class="form-control-plaintext form-control-dark">
                                <code>{{ $khieunai->giaoDich->MaGiaoDich ?? 'N/A' }}</code>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Số tiền</label>
                            <p class="form-control-plaintext form-control-dark fw-bold text-success">
                                {{ number_format($khieunai->giaoDich->SoTien, 0, ',', '.') }} đ
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Loại giao dịch</label>
                            <p class="form-control-plaintext form-control-dark">
                                <span class="badge bg-info">{{ $khieunai->giaoDich->LoaiGiaoDich }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trạng thái</label>
                            <p class="form-control-plaintext form-control-dark">
                                <span class="badge bg-warning">{{ $khieunai->giaoDich->TrangThai }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Người khiếu nại</h5>
                </div>
                <div class="card-body">
                    @if($khieunai->taiKhoan)
                        <div class="mb-3">
                            <label class="form-label text-muted">ID Tài khoản</label>
                            <p class="form-control-plaintext form-control-dark">#{{ $khieunai->TaiKhoanID }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="form-control-plaintext form-control-dark">{{ $khieunai->taiKhoan->Email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Số điện thoại</label>
                            <p class="form-control-plaintext form-control-dark">{{ $khieunai->taiKhoan->SoDienThoai ?? 'N/A' }}</p>
                        </div>
                        @if($khieunai->taiKhoan->giasu)
                            <div class="mb-3">
                                <label class="form-label text-muted">Họ tên</label>
                                <p class="form-control-plaintext form-control-dark">{{ $khieunai->taiKhoan->giasu->HoTen }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Vai trò</label>
                                <p class="form-control-plaintext form-control-dark">
                                    <span class="badge bg-primary">Gia sư</span>
                                </p>
                            </div>
                        @elseif($khieunai->taiKhoan->nguoihoc)
                            <div class="mb-3">
                                <label class="form-label text-muted">Họ tên</label>
                                <p class="form-control-plaintext form-control-dark">{{ $khieunai->taiKhoan->nguoihoc->HoTen }}</p>
                            </div>
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
