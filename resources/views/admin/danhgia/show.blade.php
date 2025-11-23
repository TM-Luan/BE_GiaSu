@extends('admin.layouts.master')

@section('title', 'Chi tiết Đánh giá #' . $danhGia->DanhGiaID)

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Chi tiết Đánh giá #{{ $danhGia->DanhGiaID }}</h4>
                <a href="{{ route('admin.danhgia.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <th>Điểm số</th>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star" style="color: {{ $i <= $danhGia->DiemSo ? 'gold' : 'lightgray' }};"></i>
                                @endfor
                                ({{ $danhGia->DiemSo }} sao)
                            </td>
                        </tr>
                        <tr>
                            <th>Học viên đánh giá</th>
                            <td>
                                {{ $danhGia->taiKhoan->nguoiHoc->HoTen ?? 'N/A' }} 
                                <small class="text-muted">({{ $danhGia->taiKhoan->Email ?? '' }})</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Gia sư được đánh giá</th>
                            <td>
                                {{ $danhGia->lop->giaSu->HoTen ?? 'N/A' }} 
                                <small class="text-muted">({{ $danhGia->lop->giaSu->taiKhoan->Email ?? '' }})</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Lớp học liên quan</th>
                            <td>
                                ID: {{ $danhGia->LopYeuCauID }} <br>
                                Môn: {{ $danhGia->lop->monHoc->TenMon ?? '' }} - {{ $danhGia->lop->khoiLop->BacHoc ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày đánh giá</th>
                            <td>{{ \Carbon\Carbon::parse($danhGia->NgayDanhGia)->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Số lần sửa</th>
                            <td>{{ $danhGia->LanSua }}</td>
                        </tr>
                        <tr>
                            <th>Bình luận chi tiết</th>
                            <td>{{ $danhGia->BinhLuan ?? 'Không có bình luận' }}</td>
                        </tr>
                        @if($danhGia->DiemSo <= 2 && empty($danhGia->BinhLuan))
                            <tr>
                                <td colspan="2" class="text-danger fw-bold">
                                    CẢNH BÁO: Đánh giá thấp ({{ $danhGia->DiemSo }} sao) nhưng không có lý do!
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                <div class="mt-4 border-top pt-3">
                    <form action="{{ route('admin.danhgia.destroy', $danhGia->DanhGiaID) }}" method="POST" onsubmit="return confirm('Xác nhận xóa vĩnh viễn đánh giá này?');" class="text-end">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa đánh giá</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection