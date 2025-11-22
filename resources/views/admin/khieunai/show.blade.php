@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-eye me-2"></i> Chi tiết Khiếu nại #{{ $khieunai->KhieuNaiID }}
        </h3>
        <div>
            <a href="{{ route('admin.khieunai.edit', $khieunai->KhieuNaiID) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen me-2"></i> Xử lý
            </a>
            <a href="{{ route('admin.khieunai.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            {{-- Card Nội dung --}}
            <div class="card mb-4 border-secondary">
                <div class="card-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="fa-solid fa-triangle-exclamation me-2"></i>Nội dung Khiếu nại</h5>
                    @php
                        $statusColors = [
                            'TiepNhan' => 'bg-secondary', // Đổi thành màu xám trung tính
                            'DangXuLy' => 'bg-warning text-dark',
                            'DaGiaiQuyet' => 'bg-success',
                            'TuChoi' => 'bg-danger'
                        ];
                        // Text hiển thị trạng thái
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
                </div>
                <div class="card-body bg-dark">
                    <div class="mb-4">
                        <label class="form-label text-white-50">Nội dung khiếu nại</label>
                        <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary">
                            <p class="mb-0 text-white" style="white-space: pre-line;">{{ $khieunai->NoiDung }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">Ngày tạo</label>
                            <p class="form-control-plaintext text-white fw-bold">
                                {{ $khieunai->NgayTao ? $khieunai->NgayTao->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">Ngày xử lý</label>
                            <p class="form-control-plaintext text-white fw-bold">
                                {{ $khieunai->NgayXuLy ? $khieunai->NgayXuLy->format('d/m/Y H:i') : 'Chưa xử lý' }}
                            </p>
                        </div>
                    </div>

                    {{-- PHẦN NÀY ĐÃ ĐƯỢC CHỈNH SỬA MÀU SẮC --}}
                    @if($khieunai->PhanHoi)
                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">Phản hồi của Admin</label>
                        {{-- Dùng style giống hệt box nội dung bên trên: Nền đen mờ, viền xám --}}
                        <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary">
                            <p class="mb-0 text-white" style="white-space: pre-line;">{{ $khieunai->PhanHoi }}</p>
                        </div>
                    </div>
                    @endif

                    @if($khieunai->GhiChu)
                    <div class="mb-3">
                        <label class="form-label text-white-50">Ghi chú nội bộ</label>
                        <div class="p-3 bg-black bg-opacity-25 rounded border border-secondary">
                            <p class="mb-0 text-white-50" style="white-space: pre-line;">{{ $khieunai->GhiChu }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- (Giữ nguyên các phần Lớp học và Giao dịch bên dưới...) --}}
            {{-- Bạn có thể copy lại phần code Lớp học và Giao dịch từ câu trả lời trước, 
                 chỉ cần đảm bảo các thẻ card đều có class "bg-dark border-secondary" --}}
        </div>

        <div class="col-md-4">
             {{-- Card Thông tin người dùng --}}
            <div class="card mb-4 border-secondary">
                <div class="card-header bg-dark border-bottom border-secondary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Người khiếu nại</h5>
                </div>
                <div class="card-body bg-dark">
                    @if($khieunai->taiKhoan)
                        <div class="mb-3">
                            <label class="form-label text-white-50">Email</label>
                            <p class="text-white fw-bold">{{ $khieunai->taiKhoan->Email }}</p>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-white-50">Vai trò</label>
                            @if($khieunai->taiKhoan->giasu)
                                <span class="badge bg-secondary border border-light">Gia sư</span>
                            @elseif($khieunai->taiKhoan->nguoihoc)
                                <span class="badge bg-secondary border border-light">Người học</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection