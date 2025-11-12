@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-plus me-2"></i> Thêm Gia sư mới
        </h3>
        <a href="{{ route('admin.giasu.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- Vẫn giữ khối lỗi chung này --}}
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

            <form action="{{ route('admin.giasu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h5 class="text-white mb-3">Thông tin Đăng nhập (Bảng TaiKhoan)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Bắt buộc)</label>
                        {{-- Thêm class @error và thẻ @error --}}
                        <input type="email" name="Email" class="form-control form-control-dark @error('Email') is-invalid @enderror" 
                               value="{{ old('Email') }}" required>
                        @error('Email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control form-control-dark @error('SoDienThoai') is-invalid @enderror" 
                               value="{{ old('SoDienThoai') }}" placeholder="VD: 0912345678">
                        @error('SoDienThoai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Trạng thái (Bắt buộc)</label>
                        <select name="TrangThai" class="form-select form-select-dark @error('TrangThai') is-invalid @enderror" required>
                            <option value="1" {{ old('TrangThai', '1') == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('TrangThai') == 0 ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                         @error('TrangThai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="border-secondary">

                <h5 class="text-white mb-3">Mật khẩu (Bắt buộc)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="MatKhau" class="form-control form-control-dark @error('MatKhau') is-invalid @enderror" 
                               placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                               autocomplete="new-password" required>
                        @error('MatKhau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Xác nhận Mật khẩu</label>
                        <input type="password" name="MatKhau_confirmation" class="form-control form-control-dark"
                               placeholder="Nhập lại mật khẩu mới"
                               autocomplete="new-password" required>
                    </div>
                </div>
                <hr class="border-secondary">

                <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng GiaSu)</h5>
                <div class="row">
                     <div class="col-md-5 mb-3">
                        <label class="form-label">Họ tên (Bắt buộc)</label>
                        <input type="text" name="HoTen" class="form-control form-control-dark @error('HoTen') is-invalid @enderror" 
                               value="{{ old('HoTen') }}" required>
                        @error('HoTen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="GioiTinh" class="form-select form-select-dark @error('GioiTinh') is-invalid @enderror">
                            <option value="">Chưa chọn</option>
                            <option value="Nam" {{ old('GioiTinh') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ old('GioiTinh') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('GioiTinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="NgaySinh" class="form-control form-control-dark @error('NgaySinh') is-invalid @enderror" 
                               value="{{ old('NgaySinh') }}">
                        @error('NgaySinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control form-control-dark @error('DiaChi') is-invalid @enderror" 
                               value="{{ old('DiaChi') }}">
                        @error('DiaChi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- Các trường chuyên môn và ảnh không bắt buộc nên có thể bỏ qua @error, trừ khi bạn muốn thêm --}}

                <hr class="border-secondary">
                <h5 class="text-white mb-3">Ảnh (Tùy chọn)</h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input class="form-control form-control-dark @error('AnhDaiDien') is-invalid @enderror" type="file" name="AnhDaiDien">
                        @error('AnhDaiDien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt trước</label>
                        <input class="form-control form-control-dark @error('AnhCCCD_MatTruoc') is-invalid @enderror" type="file" name="AnhCCCD_MatTruoc">
                        @error('AnhCCCD_MatTruoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt sau</label>
                        <input class="form-control form-control-dark @error('AnhCCCD_MatSau') is-invalid @enderror" type="file" name="AnhCCCD_MatSau">
                         @error('AnhCCCD_MatSau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh Bằng cấp</label>
                        <input class="form-control form-control-dark @error('AnhBangCap') is-invalid @enderror" type="file" name="AnhBangCap">
                         @error('AnhBangCap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Lưu & Tạo mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection