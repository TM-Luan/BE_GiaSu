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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-file-alt me-2"></i>Nội dung Khiếu nại</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nội dung từ người dùng</label>
                        <div class="p-3 bg-dark rounded border border-secondary">
                            <p class="mb-0 text-white" style="white-space: pre-line;">{{ $khieunai->NoiDung }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted">Người khiếu nại</label>
                            <p class="text-white">{{ $khieunai->taiKhoan->Email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Ngày tạo</label>
                            <p class="text-white">
                                @if($khieunai->NgayTao)
                                    {{ $khieunai->NgayTao->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.khieunai.update', $khieunai->KhieuNaiID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h5 class="text-white mb-3"><i class="fa-solid fa-tasks me-2"></i>Xử lý Khiếu nại</h5>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="TrangThai" class="form-select form-select-dark" required>
                                <option value="TiepNhan" {{ $khieunai->TrangThai == 'TiepNhan' ? 'selected' : '' }}>Tiếp nhận</option>
                                <option value="DangXuLy" {{ $khieunai->TrangThai == 'DangXuLy' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="DaGiaiQuyet" {{ $khieunai->TrangThai == 'DaGiaiQuyet' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="TuChoi" {{ $khieunai->TrangThai == 'TuChoi' ? 'selected' : '' }}>Từ chối</option>
                            </select>
                            <small class="text-muted">Cập nhật trạng thái xử lý khiếu nại</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phản hồi cho người dùng</label>
                            <textarea name="PhanHoi" class="form-control form-control-dark" rows="5" 
                                      placeholder="Nhập nội dung phản hồi sẽ gửi đến người khiếu nại...">{{ old('PhanHoi', $khieunai->PhanHoi) }}</textarea>
                            <small class="text-muted">Nội dung này sẽ hiển thị cho người dùng</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú nội bộ (không hiển thị cho người dùng)</label>
                            <textarea name="GhiChu" class="form-control form-control-dark" rows="3" 
                                      placeholder="Ghi chú nội bộ của admin...">{{ old('GhiChu', $khieunai->GhiChu) }}</textarea>
                            <small class="text-muted">Chỉ admin có thể xem ghi chú này</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> Hành động này sẽ cập nhật thời gian xử lý và có thể gửi thông báo đến người khiếu nại.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.khieunai.show', $khieunai->KhieuNaiID) }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-2"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-2"></i> Lưu xử lý
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Thông tin liên quan</h5>
                </div>
                <div class="card-body">
                    @if($khieunai->lop)
                        <div class="mb-3">
                            <label class="form-label text-muted">Lớp học</label>
                            <p class="form-control-plaintext form-control-dark">
                                <a href="{{ route('admin.lophoc.show', $khieunai->LopYeuCauID) }}" class="text-info">
                                    #{{ $khieunai->LopYeuCauID }} - {{ $khieunai->lop->monHoc->TenMon ?? 'N/A' }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if($khieunai->giaoDich)
                        <div class="mb-3">
                            <label class="form-label text-muted">Giao dịch</label>
                            <p class="form-control-plaintext form-control-dark">
                                <a href="{{ route('admin.giaodich.show', $khieunai->GiaoDichID) }}" class="text-info">
                                    {{ $khieunai->giaoDich->MaGiaoDich ?? '#' . $khieunai->GiaoDichID }}
                                </a>
                            </p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted">Trạng thái hiện tại</label>
                        <p class="form-control-plaintext form-control-dark">
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
                        </p>
                    </div>

                    @if($khieunai->NgayXuLy)
                        <div class="mb-3">
                            <label class="form-label text-muted">Lần xử lý gần nhất</label>
                            <p class="form-control-plaintext form-control-dark">
                                {{ $khieunai->NgayXuLy->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Người khiếu nại</h5>
                </div>
                <div class="card-body">
                    @if($khieunai->taiKhoan)
                        <div class="mb-2">
                            <small class="text-muted">Email:</small>
                            <p class="text-white mb-1">{{ $khieunai->taiKhoan->Email }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">SĐT:</small>
                            <p class="text-white mb-1">{{ $khieunai->taiKhoan->SoDienThoai ?? 'N/A' }}</p>
                        </div>
                        @if($khieunai->taiKhoan->giasu)
                            <div class="mb-2">
                                <small class="text-muted">Vai trò:</small>
                                <p class="mb-0"><span class="badge bg-primary">Gia sư</span></p>
                            </div>
                        @elseif($khieunai->taiKhoan->nguoihoc)
                            <div class="mb-2">
                                <small class="text-muted">Vai trò:</small>
                                <p class="mb-0"><span class="badge bg-info">Người học</span></p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
