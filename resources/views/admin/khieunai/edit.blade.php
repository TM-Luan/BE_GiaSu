@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-pen me-2"></i> Xử lý Khiếu nại #{{ $khieunai->KhieuNaiID }}
        </h3>
        <a href="{{ route('admin.khieunai.show', $khieunai->KhieuNaiID) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- CỘT TRÁI --}}
        <div class="col-md-8">
            {{-- Nội dung khiếu nại --}}
            <div class="card mb-4 border-secondary">
                <div class="card-header bg-dark border-bottom border-secondary text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-file-alt me-2"></i>Nội dung từ người dùng</h6>
                </div>
                <div class="card-body bg-dark text-light">
                    <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary">
                        <p class="mb-0" style="white-space: pre-line;">{{ $khieunai->NoiDung }}</p>
                    </div>
                </div>
            </div>

            {{-- FORM XỬ LÝ (Đã làm đơn giản hóa màu sắc) --}}
            <div class="card border-secondary">
                <div class="card-header bg-dark border-bottom border-secondary text-white">
                     <h5 class="mb-0"><i class="fa-solid fa-gavel me-2"></i>Thông tin Xử lý</h5>
                </div>
                <div class="card-body bg-dark">
                    <form action="{{ route('admin.khieunai.update', $khieunai->KhieuNaiID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- 1. Trạng thái --}}
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">1. Trạng thái xử lý <span class="text-danger">*</span></label>
                            <select name="TrangThai" class="form-select form-select-lg bg-dark text-white border-secondary" required>
                                <option value="TiepNhan" {{ $khieunai->TrangThai == 'TiepNhan' ? 'selected' : '' }}>Tiếp nhận</option>
                                <option value="DangXuLy" {{ $khieunai->TrangThai == 'DangXuLy' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="DaGiaiQuyet" {{ $khieunai->TrangThai == 'DaGiaiQuyet' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="TuChoi" {{ $khieunai->TrangThai == 'TuChoi' ? 'selected' : '' }}>Từ chối / Hủy</option>
                            </select>
                        </div>

                        {{-- 2. Ghi chú nội bộ --}}
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">
                                2. Phương án giải quyết / Ghi chú nội bộ
                            </label>
                            <small class="text-white-50 d-block mb-2">
                                (Lưu vào cột GiaiQuyet - Dành cho Admin)
                            </small>
                            <textarea name="GiaiQuyet" class="form-control form-control-dark bg-dark text-white border-secondary" rows="4">{{ old('GiaiQuyet', $khieunai->GiaiQuyet) }}</textarea>
                        </div>

                        {{-- 3. Phản hồi user --}}
                        <div class="mb-4">
                            {{-- Đã bỏ text-success (màu xanh), chuyển về text-white --}}
                            <label class="form-label text-white fw-bold">
                                3. Phản hồi cho người dùng
                            </label>
                            <small class="text-white-50 d-block mb-2">
                                (Lưu vào cột PhanHoi - Người dùng sẽ thấy nội dung này)
                            </small>
                            {{-- Đã bỏ border-success, chuyển về border-secondary --}}
                            <textarea name="PhanHoi" class="form-control form-control-dark bg-dark text-white border-secondary" rows="3">{{ old('PhanHoi', $khieunai->PhanHoi) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top border-secondary pt-3">
                            <a href="{{ route('admin.khieunai.show', $khieunai->KhieuNaiID) }}" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-save me-2"></i> Lưu Xử Lý
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-md-4">
            <div class="card mb-4 border-secondary">
                <div class="card-header bg-dark border-bottom border-secondary text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Thông tin liên quan</h6>
                </div>
                <div class="card-body bg-dark">
                    <p class="mb-1 text-white-50">Người gửi:</p>
                    <p class="text-white fw-bold mb-3">{{ $khieunai->taiKhoan->Email ?? 'N/A' }}</p>
                    
                    <hr class="border-secondary">

                    <p class="mb-1 text-white-50">Ngày tạo:</p>
                    <p class="text-white mb-3">
                        {{ \Carbon\Carbon::parse($khieunai->NgayTao)->format('H:i d/m/Y') }}
                    </p>

                    @if($khieunai->lop)
                        <hr class="border-secondary">
                        <p class="mb-1 text-white-50">Lớp học:</p>
                        <a href="{{ route('admin.lophoc.show', $khieunai->LopYeuCauID) }}" class="btn btn-outline-light btn-sm w-100 text-start border-secondary">
                            <i class="fa-solid fa-external-link-alt me-2"></i> Xem lớp #{{ $khieunai->LopYeuCauID }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection