@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa Lớp học #{{ $lophoc->LopYeuCauID }}
        </h3>
        <a href="{{ route('admin.lophoc.show', $lophoc->LopYeuCauID) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.lophoc.update', $lophoc->LopYeuCauID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-circle-info me-1"></i> Trạng thái lớp học <span class="text-danger">*</span>
                        </label>
                        <select name="TrangThai" class="form-select form-select-dark" required>
                            <option value="DangHoc" {{ $lophoc->TrangThai == 'DangHoc' ? 'selected' : '' }}>Đang học - Lớp đang diễn ra</option>
                            <option value="TimGiaSu" {{ $lophoc->TrangThai == 'TimGiaSu' ? 'selected' : '' }}>Tìm gia sư - Chờ gia sư nhận</option>
                            <option value="ChoDuyet" {{ $lophoc->TrangThai == 'ChoDuyet' ? 'selected' : '' }}>Chờ duyệt - Đang xem xét</option>
                            <option value="HoanThanh" {{ $lophoc->TrangThai == 'HoanThanh' ? 'selected' : '' }}>Hoàn thành - Đã kết thúc</option>
                            <option value="DaHuy" {{ $lophoc->TrangThai == 'DaHuy' ? 'selected' : '' }}>Đã hủy - Không diễn ra</option>
                        </select>
                        <small class="text-white 50">Trạng thái hiện tại của lớp học</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-laptop me-1"></i> Hình thức dạy học
                        </label>
                        <select name="HinhThuc" class="form-select form-select-dark">
                            <option value="Online" {{ $lophoc->HinhThuc == 'Online' ? 'selected' : '' }}>Online - Học qua mạng</option>
                            <option value="Offline" {{ $lophoc->HinhThuc == 'Offline' ? 'selected' : '' }}>Offline - Gặp mặt trực tiếp</option>
                        </select>
                        <small class="text-white 50">Phương thức giảng dạy</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-money-bill-wave me-1"></i> Học phí (đ/buổi)
                        </label>
                        <input type="number" name="HocPhi" class="form-control form-control-dark" 
                               value="{{ old('HocPhi', $lophoc->HocPhi) }}" min="0" step="1000">
                        <small class="text-white 50">Học phí cho mỗi buổi học</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-clock me-1"></i> Thời lượng (phút/buổi)
                        </label>
                        <input type="number" name="ThoiLuong" class="form-control form-control-dark" 
                               value="{{ old('ThoiLuong', $lophoc->ThoiLuong ?? 90) }}" min="30" max="300">
                        <small class="text-white 50">Thời gian mỗi buổi học (30-300 phút)</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-list-ol me-1"></i> Số lượng buổi học
                        </label>
                        <input type="number" name="SoLuong" class="form-control form-control-dark" 
                               value="{{ old('SoLuong', $lophoc->SoLuong ?? 1) }}" min="1" max="100">
                        <small class="text-white 50">Tổng số buổi của khóa học (1-100 buổi)</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            <i class="fa-solid fa-align-left me-1"></i> Mô tả / Ghi chú
                        </label>
                        <textarea name="MoTa" class="form-control form-control-dark" rows="4">{{ old('MoTa', $lophoc->MoTa) }}</textarea>
                        <small class="text-white 50">Thông tin bổ sung về lớp học</small>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <div class="mb-3">
                    <h5 class="text-white mb-3"><i class="fa-solid fa-info-circle me-2"></i>Thông tin không thể chỉnh sửa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1">
                                <i class="fa-solid fa-user me-2 text-white 50"></i>
                                <span class="text-white-50">Người học:</span> 
                                <strong class="text-white ms-2">{{ $lophoc->nguoiHoc->HoTen ?? 'Chưa có thông tin' }}</strong>
                            </p>
                            <p class="mb-0 ms-4 ps-1">
                                <small class="text-white-50">
                                    <i class="fa-solid fa-envelope me-1"></i> Email: 
                                    <span class="text-white">{{ $lophoc->nguoiHoc->taiKhoan->Email ?? 'Chưa có email' }}</span>
                                </small>
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <p class="mb-0">
                                <i class="fa-solid fa-book me-2 text-white 50"></i>
                                <span class="text-white-50">Môn học:</span> 
                                <strong class="text-white ms-2">{{ $lophoc->monHoc->TenMon ?? 'Chưa có môn học' }}</strong>
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <p class="mb-0">
                                <i class="fa-solid fa-graduation-cap me-2 text-white 50"></i>
                                <span class="text-white-50">Khối lớp:</span> 
                                <strong class="text-white ms-2">{{ $lophoc->khoiLop->BacHoc ?? 'Chưa xác định' }}</strong>
                            </p>
                        </div>
                        @if($lophoc->giaSu)
                        <div class="col-md-6 mb-3">
                            <p class="mb-1">
                                <i class="fa-solid fa-chalkboard-user me-2 text-white 50"></i>
                                <span class="text-white-50">Gia sư:</span> 
                                <strong class="text-white ms-2">{{ $lophoc->giaSu->HoTen }}</strong>
                            </p>
                            <p class="mb-0 ms-4 ps-1">
                                <small class="text-white-50">
                                    <i class="fa-solid fa-envelope me-1"></i> Email: 
                                    <span class="text-white">{{ $lophoc->giaSu->taiKhoan->Email ?? 'Chưa có email' }}</span>
                                </small>
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fa-solid fa-lock me-2"></i>
                        <small>Những thông tin trên không thể thay đổi sau khi lớp học được tạo để đảm bảo tính nhất quán của dữ liệu.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.lophoc.show', $lophoc->LopYeuCauID) }}" class="btn btn-secondary">
                        <i class="fa-solid fa-times me-2"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
