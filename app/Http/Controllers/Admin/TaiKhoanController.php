<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;

class TaiKhoanController extends Controller
{
    /**
     * Hiển thị danh sách Tài khoản.
     */
    public function index()
    {
        // Lấy tất cả tài khoản, sử dụng Eager Loading để lấy VaiTro (PhanQuyen)
        // và phân trang
        $taiKhoans = TaiKhoan::with('phanquyen.vaitro')
                            ->orderByDesc('TaiKhoanID')
                            ->paginate(10); 
                            
        // VaiTroID: 1=Admin, 2=GiaSu, 3=NguoiHoc
        return view('admin.taikhoan.index', [
            'taiKhoans' => $taiKhoans
        ]);
    }
    
    // Các phương thức khác (create, store, show, edit, update, destroy) sẽ được bổ sung sau
}