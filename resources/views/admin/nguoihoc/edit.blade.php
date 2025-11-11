@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-pen-to-square me-2"></i> Chỉnh sửa Người học: {{ $taiKhoan->nguoihoc->HoTen ?? $taiKhoan->Email }}
        </h3>
        <a href="{{ route('admin.nguoihoc.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Hiển thị lỗi validation -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.nguoihoc.update', $taiKhoan->TaiKhoanID) }}" method="POST">
                @csrf <!-- Bắt buộc -->
                @method('PUT') <!-- Bắt buộc cho route resource update -->

                <h5 class="text-white mb-3">Thông tin Cơ bản (Bảng TaiKhoan)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Bắt buộc)</label>
                        <input type="email" name="Email" class="form-control form-control-dark" 
                               value="{{ old('Email', $taiKhoan->Email) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control form-control-dark" 
                               value="{{ old('SoDienThoai', $taiKhoan->SoDienThoai) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trạng thái (Bắt buộc)</label>
                        <select name="TrangThai" class="form-select form-select-dark" required>
                            <option value="1" {{ old('TrangThai', $taiKhoan->TrangThai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('TrangThai', $taiKhoan->TrangThai) == 0 ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                    </div>
                </div>

                <hr class="border-secondary">

                <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng NguoiHoc) - ĐÃ CẬP NHẬT</h5>
                <div class="row">
                    <!-- Hàng 1: Tên, Giới tính, Ngày sinh -->
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Họ tên (Bắt buộc)</label>
                        <input type="text" name="HoTen" class="form-control form-control-dark" 
                               value="{{ old('HoTen', $taiKhoan->nguoihoc->HoTen ?? '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="GioiTinh" class="form-select form-select-dark">
                            <option value="">Chưa chọn</option>
                            <option value="Nam" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="NgaySinh" class="form-control form-control-dark" 
                               value="{{ old('NgaySinh', $taiKhoan->nguoihoc->NgaySinh ?? '') }}">
                    </div>

                    <!-- Hàng 2: Địa chỉ -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control form-control-dark" 
                               value="{{ old('DiaChi', $taiKhoan->nguoihoc->DiaChi ?? '') }}">
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection