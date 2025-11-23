<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;

class DanhGiaController extends Controller
{
    /**
     * Hiển thị danh sách tất cả đánh giá
     */
    public function index(Request $request)
    {
        $query = DanhGia::query();
        
        // Eager load các mối quan hệ cần thiết cho bảng
        $query->with([
            'taiKhoan.nguoiHoc', 
            'lop.giaSu.taiKhoan',
            'lop.monHoc',
            'lop.khoiLop'
        ]);

        // Xử lý tìm kiếm
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Tìm kiếm theo nội dung bình luận
                $q->where('BinhLuan', 'like', '%' . $searchTerm . '%')
                  // Tìm kiếm theo tên học viên
                  ->orWhereHas('taiKhoan.nguoiHoc', function ($q2) use ($searchTerm) {
                      $q2->where('HoTen', 'like', '%' . $searchTerm . '%');
                  })
                  // Tìm kiếm theo tên gia sư
                  ->orWhereHas('lop.giaSu.taiKhoan', function ($q3) use ($searchTerm) {
                      $q3->where('HoTen', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Xử lý lọc theo điểm số
        if ($request->filled('diem_so')) {
            $query->where('DiemSo', $request->diem_so);
        }

        $danhGiaList = $query->orderBy('NgayDanhGia', 'desc')->paginate(15);
        
        return view('admin.danhgia.index', [
            'danhGiaList' => $danhGiaList,
            'search' => $request->search,
            'diem_so' => $request->diem_so,
        ]);
    }

    /**
     * Hiển thị chi tiết một đánh giá
     */
    public function show($id)
    {
        $danhGia = DanhGia::with([
            'taiKhoan.nguoiHoc', 
            'lop.giaSu.taiKhoan',
            'lop.monHoc',
            'lop.khoiLop'
        ])->findOrFail($id);
        
        return view('admin.danhgia.show', compact('danhGia'));
    }
    
    /**
     * Cập nhật trạng thái đánh giá (Nếu cần) - Hiện tại chưa có cột TrangThai
     * Đây là placeholder function nếu bạn muốn thêm tính năng duyệt
     */
    // public function update(Request $request, $id)
    // {
    //     $danhGia = DanhGia::findOrFail($id);
    //     $validated = $request->validate([
    //         'TrangThai' => 'required|in:Pending,Approved,Rejected',
    //     ]);
    //     $danhGia->update($validated);
    //     return redirect()->route('admin.danhgia.index')->with('success', 'Cập nhật trạng thái đánh giá thành công.');
    // }

    /**
     * Xóa đánh giá
     */
    public function destroy($id)
    {
        $danhGia = DanhGia::findOrFail($id);
        $danhGia->delete();
        return redirect()->route('admin.danhgia.index')->with('success', 'Xóa đánh giá thành công.');
    }
}