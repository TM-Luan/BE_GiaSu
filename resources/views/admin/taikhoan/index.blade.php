// File: resources/views/admin/taikhoan/index.blade.php
@extends('admin.layouts.master')

@section('content')
<div class="container mt-4">
    <h3>👤 Quản lý Tài khoản</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taiKhoans as $tk)
            <tr>
                <td>{{ $tk->TaiKhoanID }}</td>
                <td>{{ $tk->Email }}</td>
                <td>{{ $tk->SoDienThoai }}</td>
                <td>
                    @if($tk->phanquyen && $tk->phanquyen->vaitro)
                        <span class="badge 
                            @if($tk->phanquyen->VaiTroID == 1) bg-dark
                            @elseif($tk->phanquyen->VaiTroID == 2) bg-primary
                            @else bg-success
                            @endif">
                            {{ $tk->phanquyen->vaitro->TenVaiTro }}
                        </span>
                    @else
                        <span class="badge bg-secondary">Chưa phân quyền</span>
                    @endif
                </td>
                <td>{{ $tk->NgayTao }}</td>
                <td>
                    <span class="badge @if($tk->TrangThai == 1) bg-success @else bg-danger @endif">
                        {{ $tk->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa' }}
                    </span>
                </td>
                <td>
                    <a href="#" class="btn btn-sm btn-info">Sửa</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $taiKhoans->links() }}
</div>
@endsection