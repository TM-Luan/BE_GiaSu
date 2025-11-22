@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Quản lý Khiếu nại
        </h3>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.khieunai.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control form-control-dark" 
                               placeholder="Tìm kiếm theo nội dung khiếu nại..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="trangthai" class="form-select form-select-dark">
                            <option value="">Tất cả Trạng thái</option>
                            <option value="TiepNhan" {{ request('trangthai') == 'TiepNhan' ? 'selected' : '' }}>Tiếp nhận</option>
                            <option value="DangXuLy" {{ request('trangthai') == 'DangXuLy' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="DaGiaiQuyet" {{ request('trangthai') == 'DaGiaiQuyet' ? 'selected' : '' }}>Đã giải quyết</option>
                            <option value="TuChoi" {{ request('trangthai') == 'TuChoi' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-2"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người khiếu nại</th>
                            <th>Lớp học</th>
                            <th>Nội dung</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($khieunai as $kn)
                        <tr>
                            <td>{{ $kn->KhieuNaiID }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $kn->taiKhoan->Email ?? 'N/A' }}</span>
                                    <small class="text-white 50">ID: {{ $kn->TaiKhoanID }}</small>
                                </div>
                            </td>
                            <td>
                                @if($kn->lop)
                                    <small>Lớp #{{ $kn->LopYeuCauID }}</small><br>
                                    <small class="text-white 25">{{ $kn->lop->monHoc->TenMon ?? '' }}</small>
                                @else
                                    <span class="text-white 25">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    {{ Str::limit($kn->NoiDung, 80) }}
                                </div>
                            </td>
                            <td>
                                <small>{{ $kn->NgayTao ? $kn->NgayTao->format('d/m/Y H:i') : 'N/A' }}</small>
                            </td>
                            <td>
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
                                <span class="badge {{ $statusColors[$kn->TrangThai] ?? 'bg-secondary' }}">
                                    {{ $statusText[$kn->TrangThai] ?? $kn->TrangThai }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.khieunai.show', $kn->KhieuNaiID) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.khieunai.edit', $kn->KhieuNaiID) }}" class="btn btn-sm btn-outline-warning" title="Xử lý">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.khieunai.destroy', $kn->KhieuNaiID) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa khiếu nại này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-white 25 mb-3 d-block"></i>
                                <p class="text-white 25 mb-0">Không tìm thấy khiếu nại nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $khieunai->appends(request()->query())->links() }}
    </div>
</div>
@endsection
