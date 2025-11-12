@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Người học: {{ $taiKhoan->nguoihoc->HoTen ?? $taiKhoan->Email }}
        </h3>
        <a href="{{ route('admin.nguoihoc.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- Thông tin cơ bản --}}
            <h5 class="text-white mb-3">Thông tin Cơ bản (Bảng TaiKhoan)</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Email</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->Email }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Số điện thoại</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->SoDienThoai ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Trạng thái</label>
                    <div>
                        <span class="badge rounded-pill {{ $taiKhoan->TrangThai == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $taiKhoan->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa' }}
                        </span>
                    </div>
                </div>
            </div>

            <hr class="border-secondary">

            {{-- Thông tin chi tiết --}}
            <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng NguoiHoc)</h5>
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label text-muted">Họ tên</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->nguoihoc->HoTen ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Giới tính</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->nguoihoc->GioiTinh ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Ngày sinh</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->nguoihoc->NgaySinh ? \Carbon\Carbon::parse($taiKhoan->nguoihoc->NgaySinh)->format('d/m/Y') : 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-12 mt-3">
                    <label class="form-label text-muted">Địa chỉ</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->nguoihoc->DiaChi ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>

            <hr class="border-secondary">

            {{-- Ảnh --}}
            <h5 class="text-white mb-3">Ảnh Đại diện</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    @if($taiKhoan->nguoihoc?->AnhDaiDien)
                        <a href="{{ $taiKhoan->nguoihoc->AnhDaiDien }}" target="_blank">
                            <img src="{{ $taiKhoan->nguoihoc->AnhDaiDien }}" alt="Ảnh đại diện" class="img-fluid rounded" style="border: 1px solid #555;">
                        </a>
                    @else
                        <div class="text-muted fst-italic">Chưa có ảnh</div>
                    @endif
                </div>
            </div>

            <div class="text-end mt-4">
                 <a href="{{ route('admin.nguoihoc.edit', $taiKhoan->TaiKhoanID) }}" class="btn btn-warning">
                    <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                </a>
            </div>

        </div>
    </div>
</div>
@endsection