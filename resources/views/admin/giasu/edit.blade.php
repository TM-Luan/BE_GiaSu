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

            <form action="{{ route('admin.giasu.update', $taiKhoan->TaiKhoanID) }}" method="POST" enctype="multipart/form-data">
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
                            <option value="1" {{ (string)old('TrangThai', $taiKhoan->TrangThai) === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="2" {{ (string)old('TrangThai', $taiKhoan->TrangThai) === '2' ? 'selected' : '' }}>Bị khóa</option>
                            <option value="0" {{ (string)old('TrangThai', $taiKhoan->TrangThai) === '0' ? 'selected' : '' }}>Chờ duyệt</option>
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

                <h5 class="text-white mb-3">Thông tin Chi tiết (Bảng GiaSu)</h5>
                <div class="row">
                     <div class="col-md-5 mb-3">
                        <label class="form-label">Họ tên (Bắt buộc)</label>
                        <input type="text" name="HoTen" class="form-control form-control-dark @error('HoTen') is-invalid @enderror" 
                               value="{{ old('HoTen', $taiKhoan->giasu->HoTen ?? '') }}" required>
                        @error('HoTen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="GioiTinh" class="form-select form-select-dark @error('GioiTinh') is-invalid @enderror">
                            <option value="">Chưa chọn</option>
                            <option value="Nam" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ old('GioiTinh', $taiKhoan->giasu->GioiTinh ?? '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                         @error('GioiTinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="NgaySinh" class="form-control form-control-dark @error('NgaySinh') is-invalid @enderror" 
                               value="{{ old('NgaySinh', $taiKhoan->giasu->NgaySinh ?? '') }}">
                        @error('NgaySinh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control form-control-dark @error('DiaChi') is-invalid @enderror" 
                               value="{{ old('DiaChi', $taiKhoan->giasu->DiaChi ?? '') }}">
                         @error('DiaChi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- ================================================= --}}
                {{-- ===== BẮT ĐẦU: THÊM KHỐI CODE CÒN THIẾU NÀY ===== --}}
                {{-- ================================================= --}}
                <hr class="border-secondary">
                <h5 class="text-white mb-3">Thông tin Chuyên môn & Xác thực</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bằng cấp</label>
                        <input type="text" name="BangCap" class="form-control form-control-dark @error('BangCap') is-invalid @enderror" 
                               value="{{ old('BangCap', $taiKhoan->giasu->BangCap ?? '') }}">
                        @error('BangCap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trường đào tạo</label>
                        <input type="text" name="TruongDaoTao" class="form-control form-control-dark @error('TruongDaoTao') is-invalid @enderror" 
                               value="{{ old('TruongDaoTao', $taiKhoan->giasu->TruongDaoTao ?? '') }}">
                        @error('TruongDaoTao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chuyên ngành</label>
                        <input type="text" name="ChuyenNganh" class="form-control form-control-dark @error('ChuyenNganh') is-invalid @enderror" 
                               value="{{ old('ChuyenNganh', $taiKhoan->giasu->ChuyenNganh ?? '') }}">
                         @error('ChuyenNganh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kinh nghiệm</label>
                        <input type="text" name="KinhNghiem" class="form-control form-control-dark @error('KinhNghiem') is-invalid @enderror" 
                               value="{{ old('KinhNghiem', $taiKhoan->giasu->KinhNghiem ?? '') }}" placeholder="VD: 2 năm kinh nghiệm">
                         @error('KinhNghiem')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-12 mb-3">
                        <label class="form-label">Thành tích</label>
                        <textarea name="ThanhTich" class="form-control form-control-dark @error('ThanhTich') is-invalid @enderror" rows="3">{{ old('ThanhTich', $taiKhoan->giasu->ThanhTich ?? '') }}</textarea>
                         @error('ThanhTich')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- =============================================== --}}
                {{-- ===== KẾT THÚC: KHỐI CODE CÒN THIẾU NÀY ===== --}}
                {{-- =============================================== --}}

                <hr class="border-secondary">
                <h5 class="text-white mb-3">Ảnh (Tùy chọn)</h5>
                
                {{-- ... (Phần hiển thị ảnh cũ của bạn) ... --}}
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        @if($taiKhoan->giasu?->AnhDaiDien)
                            <img src="{{ $taiKhoan->giasu->AnhDaiDien }}" alt="Ảnh đại diện" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @endif
                        <input class="form-control form-control-dark @error('AnhDaiDien') is-invalid @enderror" type="file" name="AnhDaiDien">
                        @error('AnhDaiDien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt trước</label>
                        @if($taiKhoan->giasu?->AnhCCCD_MatTruoc)
                             <img src="{{ $taiKhoan->giasu->AnhCCCD_MatTruoc }}" alt="CCCD Mặt trước" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @endif
                        <input class="form-control form-control-dark @error('AnhCCCD_MatTruoc') is-invalid @enderror" type="file" name="AnhCCCD_MatTruoc">
                        @error('AnhCCCD_MatTruoc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- (Tương tự cho 2 ảnh còn lại) --}}
                     <div class="col-md-3 mb-3">
                        <label class="form-label">CCCD Mặt sau</label>
                        @if($taiKhoan->giasu?->AnhCCCD_MatSau)
                             <img src="{{ $taiKhoan->giasu->AnhCCCD_MatSau }}" alt="CCCD Mặt sau" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @endif
                        <input class="form-control form-control-dark @error('AnhCCCD_MatSau') is-invalid @enderror" type="file" name="AnhCCCD_MatSau">
                        @error('AnhCCCD_MatSau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-3 mb-3">
                        <label class="form-label">Ảnh Bằng cấp</label>
                        @if($taiKhoan->giasu?->AnhBangCap)
                             <img src="{{ $taiKhoan->giasu?->AnhBangCap }}" alt="Ảnh Bằng cấp" class="img-fluid rounded mb-2" style="max-height: 150px; border: 1px solid #555;">
                        @endif
                        <input class="form-control form-control-dark @error('AnhBangCap') is-invalid @enderror" type="file" name="AnhBangCap">
                        @error('AnhBangCap')
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