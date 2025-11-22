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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p class="fw-bold">Đã xảy ra lỗi khi nhập liệu:</p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.nguoihoc.update', $taiKhoan->TaiKhoanID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h5 class="text-white mb-3">Thông tin Cơ bản (Bảng TaiKhoan)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Bắt buộc)</label>
                        <input type="email" name="Email" class="form-control form-control-dark @error('Email') is-invalid @enderror" 
                               value="{{ old('Email', $taiKhoan->Email) }}" required>
                        @error('Email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control form-control-dark @error('SoDienThoai') is-invalid @enderror" 
                               value="{{ old('SoDienThoai', $taiKhoan->SoDienThoai) }}" placeholder="VD: 0912345678">
                        @error('SoDienThoai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trạng thái (Bắt buộc)</label>
                        <select name="TrangThai" class="form-select form-select-dark @error('TrangThai') is-invalid @enderror" required>
                            <option value="1" {{ old('TrangThai', $taiKhoan->TrangThai) == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('TrangThai', $taiKhoan->TrangThai) == 0 ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                        @error('TrangThai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="border-secondary">

                <h5 class="text-white mb-3">Đổi Mật khẩu (Tùy chọn)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="MatKhau" class="form-control form-control-dark @error('MatKhau') is-invalid @enderror" 
                               placeholder="Bỏ trống nếu không đổi"
                               autocomplete="new-password">
                        @error('MatKhau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Xác nhận Mật khẩu mới</label>
                        <input type="password" name="MatKhau_confirmation" class="form-control form-control-dark"
                               placeholder="Nhập lại mật khẩu mới"
                               autocomplete="new-password">
                    </div>
                </div>
                <hr class="border-secondary">

                <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng NguoiHoc)</h5>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh đại diện hiện tại</label>
                        @php
                            $avatarSrc = $taiKhoan->nguoihoc?->AnhDaiDien;
                            if ($avatarSrc && !str_starts_with($avatarSrc, 'http')) {
                                $avatarSrc = asset('storage/' . $avatarSrc);
                            }
                        @endphp
                        
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="Ảnh đại diện" class="img-fluid rounded" style="max-height: 150px; border: 1px solid #555;">
                        @else
                            <div class="text-white 50 fst-italic">Chưa có ảnh</div>
                        @endif
                    </div>
                    <div class="col-md-9 mb-3">
                        <label for="AnhDaiDien" class="form-label">Thay đổi Ảnh đại diện</label>
                        <input class="form-control form-control-dark @error('AnhDaiDien') is-invalid @enderror" type="file" name="AnhDaiDien" id="AnhDaiDien">
                        @error('AnhDaiDien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12"><hr class="border-secondary my-3"></div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label">Họ tên (Bắt buộc)</label>
                        <input type="text" name="HoTen" class="form-control form-control-dark @error('HoTen') is-invalid @enderror" 
                               value="{{ old('HoTen', $taiKhoan->nguoihoc->HoTen ?? '') }}" required>
                        @error('HoTen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="GioiTinh" class="form-select form-select-dark @error('GioiTinh') is-invalid @enderror">
                            <option value="">Chưa chọn</option>
                            <option value="Nam" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ old('GioiTinh', $taiKhoan->nguoihoc->GioiTinh ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('GioiTinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="NgaySinh" class="form-control form-control-dark @error('NgaySinh') is-invalid @enderror" 
                               value="{{ old('NgaySinh', $taiKhoan->nguoihoc->NgaySinh ?? '') }}">
                        @error('NgaySinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control form-control-dark @error('DiaChi') is-invalid @enderror" 
                               value="{{ old('DiaChi', $taiKhoan->nguoihoc->DiaChi ?? '') }}">
                        @error('DiaChi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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