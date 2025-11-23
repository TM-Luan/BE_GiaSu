@extends('admin.layouts.master')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Danh sách Đánh giá</h4>
                <div class="d-flex justify-content-end">
                    <form action="{{ route('admin.danhgia.index') }}" method="GET" class="d-flex me-2">
                        <select name="diem_so" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            <option value="">Lọc theo sao</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @if(request('diem_so') == $i) selected @endif>
                                    {{ $i }} sao
                                </option>
                            @endfor
                        </select>
                        <input type="search" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm tên/bình luận" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm ms-2">Tìm</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-dark">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Điểm</th>
                                <th>Học viên</th>
                                <th>Gia sư</th>
                                <th>Lớp học</th>
                                <th>Bình luận</th>
                                <th>Ngày đánh giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($danhGiaList as $dg)
                                <tr>
                                    <td>{{ $dg->DanhGiaID }}</td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star" style="color: {{ $i <= $dg->DiemSo ? 'gold' : 'lightgray' }};"></i>
                                        @endfor
                                        ({{ $dg->DiemSo }})
                                    </td>
                                    <td>
                                        {{ $dg->taiKhoan->nguoiHoc->HoTen ?? 'N/A' }} 
                                        <small class="d-block text-muted">({{ $dg->taiKhoan->Email ?? '' }})</small>
                                    </td>
                                    <td>
                                        {{ $dg->lop->giaSu->HoTen ?? 'N/A' }}
                                        <small class="d-block text-muted">({{ $dg->lop->giaSu->taiKhoan->Email ?? '' }})</small>
                                    </td>
                                    <td>
                                        {{ $dg->lop->monHoc->TenMon ?? '' }} - {{ $dg->lop->khoiLop->BacHoc ?? '' }}
                                    </td>
                                    <td>{{ Str::limit($dg->BinhLuan, 50) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dg->NgayDanhGia)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.danhgia.show', $dg->DanhGiaID) }}" class="btn btn-info btn-sm">Xem</a>
                                        <form action="{{ route('admin.danhgia.destroy', $dg->DanhGiaID) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không tìm thấy đánh giá nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $danhGiaList->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection