@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white">
            <i class="fa-solid fa-credit-card me-2"></i> Quản lý Giao dịch
        </h3>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.giaodich.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-dark" 
                               placeholder="Tìm kiếm theo mã giao dịch, email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="trangthai" class="form-select form-select-dark">
                            <option value="">Tất cả Trạng thái</option>
                            <option value="ChoXuLy" {{ request('trangthai') == 'ChoXuLy' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="ThanhCong" {{ request('trangthai') == 'ThanhCong' ? 'selected' : '' }}>Thành công</option>
                            <option value="ThatBai" {{ request('trangthai') == 'ThatBai' ? 'selected' : '' }}>Thất bại</option>
                            <option value="HoanTien" {{ request('trangthai') == 'HoanTien' ? 'selected' : '' }}>Hoàn tiền</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="loai" class="form-select form-select-dark">
                            <option value="">Loại giao dịch</option>
                            <option value="NapTien" {{ request('loai') == 'NapTien' ? 'selected' : '' }}>Nạp tiền</option>
                            <option value="RutTien" {{ request('loai') == 'RutTien' ? 'selected' : '' }}>Rút tiền</option>
                            <option value="ThanhToan" {{ request('loai') == 'ThanhToan' ? 'selected' : '' }}>Thanh toán</option>
                        </select>
                    </div>
                    <div class="col-md-2">
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
                            <th>Mã GD</th>
                            <th>Tài khoản</th>
                            <th>Loại</th>
                            <th>Số tiền (đ)</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($giaodich as $gd)
                        <tr>
                            <td>{{ $gd->GiaoDichID }}</td>
                            <td><code>{{ $gd->MaGiaoDich }}</code></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $gd->taiKhoan->Email ?? 'N/A' }}</span>
                                    <small class="text-white-50">ID: {{ $gd->TaiKhoanID }}</small>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'NapTien' => 'bg-success',
                                        'RutTien' => 'bg-warning',
                                        'ThanhToan' => 'bg-info'
                                    ];
                                @endphp
                                <span class="badge {{ $typeColors[$gd->LoaiGiaoDich] ?? 'bg-secondary' }}">
                                    {{ $gd->LoaiGiaoDich }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ number_format($gd->SoTien, 0, ',', '.') }}</td>
                            <td>
                                @if($gd->ThoiGian)
                                    <small>{{ \Carbon\Carbon::parse($gd->ThoiGian)->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-white-50">N/A</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'ChoXuLy' => 'bg-warning text-dark',
                                        'ThanhCong' => 'bg-success',
                                        'ThatBai' => 'bg-danger',
                                        'HoanTien' => 'bg-info'
                                    ];
                                    $statusText = [
                                        'ChoXuLy' => 'Chờ xử lý',
                                        'ThanhCong' => 'Thành công',
                                        'ThatBai' => 'Thất bại',
                                        'HoanTien' => 'Hoàn tiền'
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$gd->TrangThai] ?? 'bg-secondary' }}">
                                    {{ $statusText[$gd->TrangThai] ?? $gd->TrangThai }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.giaodich.show', $gd->GiaoDichID) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.giaodich.edit', $gd->GiaoDichID) }}" class="btn btn-sm btn-outline-warning" title="Cập nhật">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.giaodich.destroy', $gd->GiaoDichID) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa giao dịch này?');">
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
                            <td colspan="8" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-white-50 mb-3 d-block"></i>
                                <p class="text-white-50 mb-0">Không tìm thấy giao dịch nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $giaodich->appends(request()->query())->links() }}
    </div>
</div>
@endsection
