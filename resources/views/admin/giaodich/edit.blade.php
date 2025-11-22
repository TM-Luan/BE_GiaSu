@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-pen me-2"></i> Cập nhật Giao dịch #{{ $giaodich->GiaoDichID }}
        </h3>
        <a href="{{ route('admin.giaodich.show', $giaodich->GiaoDichID) }}" class="btn btn-outline-secondary">
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
            <form action="{{ route('admin.giaodich.update', $giaodich->GiaoDichID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>Lưu ý:</strong> Bạn chỉ có thể cập nhật trạng thái và ghi chú của giao dịch. Thông tin khác không thể thay đổi.
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="TrangThai" class="form-select form-select-dark" required>
                            <option value="ChoXuLy" {{ $giaodich->TrangThai == 'ChoXuLy' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="ThanhCong" {{ $giaodich->TrangThai == 'ThanhCong' ? 'selected' : '' }}>Thành công</option>
                            <option value="ThatBai" {{ $giaodich->TrangThai == 'ThatBai' ? 'selected' : '' }}>Thất bại</option>
                            <option value="HoanTien" {{ $giaodich->TrangThai == 'HoanTien' ? 'selected' : '' }}>Hoàn tiền</option>
                        </select>
                        <small class="text-white 50">Cập nhật trạng thái xử lý giao dịch</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mã giao dịch</label>
                        <input type="text" class="form-control form-control-dark bg-dark text-white border-secondary" value="{{ $giaodich->MaGiaoDich ?? 'N/A' }}" disabled>
                        <small class="text-white 50">Không thể thay đổi</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Ghi chú / Lý do</label>
                        <textarea name="GhiChu" class="form-control form-control-dark" rows="4" placeholder="Nhập ghi chú hoặc lý do cập nhật trạng thái...">{{ old('GhiChu', $giaodich->GhiChu) }}</textarea>
                        <small class="text-white 50">Ghi chú về giao dịch hoặc lý do thay đổi trạng thái</small>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <div class="mb-3">
                    <h5 class="text-white mb-3">Thông tin giao dịch</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-white 50">Loại giao dịch</label>
                            <p class="text-white">
                                @php
                                    $typeColors = [
                                        'NapTien' => 'bg-success',
                                        'RutTien' => 'bg-warning',
                                        'ThanhToan' => 'bg-info'
                                    ];
                                @endphp
                                <span class="badge {{ $typeColors[$giaodich->LoaiGiaoDich] ?? 'bg-secondary' }}">
                                    {{ $giaodich->LoaiGiaoDich }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-white 50">Số tiền</label>
                            <p class="text-white fw-bold fs-5">{{ number_format($giaodich->SoTien, 0, ',', '.') }} đ</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-white 50">Thời gian</label>
                            <p class="text-white">
                                @if($giaodich->ThoiGian)
                                    {{ \Carbon\Carbon::parse($giaodich->ThoiGian)->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-white 50">Tài khoản</label>
                            <p class="text-white">{{ $giaodich->taiKhoan->Email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <p class="text-white 50 mb-0"><small><i class="fa-solid fa-lock me-1"></i> Các thông tin trên không thể thay đổi</small></p>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.giaodich.show', $giaodich->GiaoDichID) }}" class="btn btn-secondary">
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
