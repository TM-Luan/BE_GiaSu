@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Gia sư: {{ $taiKhoan->giasu->HoTen ?? $taiKhoan->Email }}
        </h3>
        <a href="{{ route('admin.giasu.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- Thông tin cơ bản --}}
            <h5 class="text-white mb-3">Thông tin Cơ bản (Bảng TaiKhoan)</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-white 50">Email</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->Email }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white 50">Số điện thoại</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->SoDienThoai ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white 50">Trạng thái</label>
                    <div>
                        @if($taiKhoan->TrangThai == 1)
                            <span class="badge rounded-pill bg-success">Hoạt động</span>
                        @elseif($taiKhoan->TrangThai == 2)
                            <span class="badge rounded-pill bg-danger">Bị khóa</span>
                        @elseif($taiKhoan->TrangThai == 0)
                            <span class="badge rounded-pill bg-secondary">Chờ duyệt</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="border-secondary">

            {{-- Thông tin chi tiết --}}
            <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng GiaSu)</h5>
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label text-white 50">Họ tên</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->HoTen ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white 50">Giới tính</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->GioiTinh ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white 50">Ngày sinh</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->NgaySinh ? \Carbon\Carbon::parse($taiKhoan->giasu->NgaySinh)->format('d/m/Y') : 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-12 mt-3">
                    <label class="form-label text-white 50">Địa chỉ</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->DiaChi ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>

            <hr class="border-secondary">

            {{-- Thông tin chuyên môn --}}
            <h5 class="text-white mb-3">Thông tin Chuyên môn</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-white 50">Bằng cấp</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->BangCap ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white 50">Trường đào tạo</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->TruongDaoTao ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label text-white 50">Chuyên ngành</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->ChuyenNganh ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label text-white 50">Kinh nghiệm</label>
                    <p class="form-control-plaintext form-control-dark">{{ $taiKhoan->giasu->KinhNghiem ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label text-white 50">Thành tích</label>
                    <div class="form-control-plaintext form-control-dark" style="min-height: 100px; white-space: pre-wrap;">{{ $taiKhoan->giasu->ThanhTich ?? 'Chưa cập nhật' }}</div>
                </div>
            </div>

            <hr class="border-secondary">

            {{-- Ảnh xác thực --}}
            <h5 class="text-white mb-3">Ảnh Xác thực</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label text-white 50">Ảnh đại diện</label>
                    @if($taiKhoan->giasu?->AnhDaiDien)
                        <a href="{{ $taiKhoan->giasu->AnhDaiDien }}" target="_blank">
                            <img src="{{ $taiKhoan->giasu->AnhDaiDien }}" alt="Ảnh đại diện" class="img-fluid rounded" style="border: 1px solid #555;">
                        </a>
                    @else
                        <div class="text-white 50 fst-italic">Chưa có ảnh</div>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-white 50">CCCD Mặt trước</label>
                    @if($taiKhoan->giasu?->AnhCCCD_MatTruoc)
                         <a href="{{ $taiKhoan->giasu->AnhCCCD_MatTruoc }}" target="_blank">
                            <img src="{{ $taiKhoan->giasu->AnhCCCD_MatTruoc }}" alt="CCCD Mặt trước" class="img-fluid rounded" style="border: 1px solid #555;">
                        </a>
                    @else
                        <div class="text-white 50 fst-italic">Chưa có ảnh</div>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-white 50">CCCD Mặt sau</label>
                    @if($taiKhoan->giasu?->AnhCCCD_MatSau)
                         <a href="{{ $taiKhoan->giasu->AnhCCCD_MatSau }}" target="_blank">
                            <img src="{{ $taiKhoan->giasu->AnhCCCD_MatSau }}" alt="CCCD Mặt sau" class="img-fluid rounded" style="border: 1px solid #555;">
                        </a>
                    @else
                        <div class="text-white 50 fst-italic">Chưa có ảnh</div>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-white 50">Ảnh Bằng cấp</label>
                    @if($taiKhoan->giasu?->AnhBangCap)
                         <a href="{{ $taiKhoan->giasu->AnhBangCap }}" target="_blank">
                            <img src="{{ $taiKhoan->giasu->AnhBangCap }}" alt="Ảnh Bằng cấp" class="img-fluid rounded" style="border: 1px solid #555;">
                        </a>
                    @else
                        <div class="text-white 50 fst-italic">Chưa có ảnh</div>
                    @endif
                </div>
            </div>

            <div class="text-end mt-4">
                 <a href="{{ route('admin.giasu.edit', $taiKhoan->TaiKhoanID) }}" class="btn btn-warning">
                    <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                </a>
            </div>

        </div>
    </div>
</div>
@endsection