@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-pen-to-square me-2"></i> Chỉnh sửa Gia sư: {{ $taiKhoan->giasu->HoTen ?? $taiKhoan->Email }}
        </h3>
        <a href="{{ route('admin.giasu.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
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

            <form action="{{ route('admin.giasu.update', $taiKhoan->TaiKhoanID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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

                <h5 class="text-white mb-3">Đổi Mật khẩu (Tùy chọn)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="MatKhau" class="form-control form-control-dark" 
                               placeholder="Bỏ trống nếu không đổi"
                               autocomplete="new-password">
                        <div class="form-text text-muted">Yêu cầu tối thiểu 8 ký tự.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Xác nhận Mật khẩu mới</label>
                        <input type="password" name="MatKhau_confirmation" class="form-control form-control-dark"
                               placeholder="Nhập lại mật khẩu mới"
                               autocomplete="new-password">
                    </div>
                </div>
                <hr class="border-secondary">

                <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng GiaSu)</h5>
                
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Họ tên (Bắt buộc)</label>
                        <input type="text" name="HoTen" class="form-control form-control-dark" 
                               value="{{ old('HoTen', $taiKhoan->giasu->HoTen ?? '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="GioiTinh" class="form-select form-select-dark">
                            <option value="">Chưa chọn</option>
                            <option value="Nam" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="NgaySinh" class="form-control form-control-dark" 
                               value="{{ old('NgaySinh', $taiKhoan->giasu->NgaySinh ?? '') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control form-control-dark" 
                               value="{{ old('DiaChi', $taiKhoan->giasu->DiaChi ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bằng cấp</label>
                        <input type="text" name="BangCap" class="form-control form-control-dark" 
                               value="{{ old('BangCap', $taiKhoan->giasu->BangCap ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Trường đào tạo</label>
                        <input type="text" name="TruongDaoTao" class="form-control form-control-dark" 
                               value="{{ old('TruongDaoTao', $taiKhoan->giasu->TruongDaoTao ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Chuyên ngành</label>
                        <input type="text" name="ChuyenNganh" class="form-control form-control-dark" 
                               value="{{ old('ChuyenNganh', $taiKhoan->giasu->ChuyenNganh ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kinh nghiệm</label>
                        <input type="text" name="KinhNghiem" class="form-control form-control-dark" 
                               value="{{ old('KinhNghiem', $taiKhoan->giasu->KinhNghiem ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thành tích</label>
                        <textarea name="ThanhTich" class="form-control form-control-dark" rows="3">{{ old('ThanhTich', $taiKhoan->giasu->ThanhTich ?? '') }}</textarea>
                    </div>
                </div>

                <hr class="border-secondary">
                <h5 class="text-white mb-3">Hình ảnh (Upload để thay đổi)</h5>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        @if($taiKhoan->giasu?->AnhDaiDien)
                            <img src="{{ $taiKhoan->giasu->AnhDaiDien }}" alt="Ảnh đại diện" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @else
                            <div class="text-muted fst-italic mb-2">Chưa có ảnh</div>
                        @endif
                        <input class="form-control form-control-dark" type="file" name="AnhDaiDien">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt trước</label>
                        @if($taiKhoan->giasu?->AnhCCCD_MatTruoc)
                            <img src="{{ $taiKhoan->giasu->AnhCCCD_MatTruoc }}" alt="CCCD Mặt trước" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @else
                            <div class="text-muted fst-italic mb-2">Chưa có ảnh</div>
                        @endif
                        <input class="form-control form-control-dark" type="file" name="AnhCCCD_MatTruoc">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt sau</label>
                        @if($taiKhoan->giasu?->AnhCCCD_MatSau)
                            <img src="{{ $taiKhoan->giasu->AnhCCCD_MatSau }}" alt="CCCD Mặt sau" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @else
                            <div class="text-muted fst-italic mb-2">Chưa có ảnh</div>
                        @endif
                        <input class="form-control form-control-dark" type="file" name="AnhCCCD_MatSau">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh Bằng cấp</label>
                        @if($taiKhoan->giasu?->AnhBangCap)
                            <img src="{{ $taiKhoan->giasu->AnhBangCap }}" alt="Ảnh Bằng cấp" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @else
                            <div class="text-muted fst-italic mb-2">Chưa có ảnh</div>
                        @endif
                        <input class="form-control form-control-dark" type="file" name="AnhBangCap">
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