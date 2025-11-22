<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\KhieuNai;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class KhieuNaiController extends Controller
{
    /**
     * Lưu khiếu nại mới (API: POST /api/khieunai)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'NoiDung' => 'required|string|max:2000',
            'LopYeuCauID' => 'nullable|integer|exists:LopHocYeuCau,LopYeuCauID',
            'GiaoDichID' => 'nullable|integer|exists:giaodich,GiaoDichID',
        ]);

        // Lấy ID tài khoản đang đăng nhập (hoặc tạm test dùng cố định)
        $taiKhoanID = Auth::id() ?? $request->get('TaiKhoanID');

        if (!$taiKhoanID) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được người gửi khiếu nại!',
            ], 401);
        }

        $khieuNai = KhieuNai::create([
            'TaiKhoanID' => $taiKhoanID,
            'NoiDung' => $validated['NoiDung'],
            'LopYeuCauID' => $validated['LopYeuCauID'] ?? null,
            'GiaoDichID' => $validated['GiaoDichID'] ?? null,
            'TrangThai' => 'TiepNhan',
            'NgayTao' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi khiếu nại thành công!',
            'data' => $khieuNai
        ], 201);
    }
    /**
     * Cập nhật khiếu nại (PUT /api/khieunai/{id})
     */
    public function update(Request $request, $id)
    {
        // Tìm khiếu nại của chính user đang đăng nhập
        $khieuNai = KhieuNai::where('KhieuNaiID', $id)
                            ->where('TaiKhoanID', Auth::id()) 
                            ->first();

        if (!$khieuNai) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy khiếu nại!'], 404);
        }

        // 1. KIỂM TRA THỜI GIAN: 5 PHÚT
        $thoiGianTao = \Carbon\Carbon::parse($khieuNai->NgayTao);
        if (now()->diffInMinutes($thoiGianTao) > 5) {
            return response()->json([
                'success' => false, 
                'message' => 'Đã quá 5 phút, không thể chỉnh sửa được nữa!'
            ], 403);
        }

        // 2. KIỂM TRA TRẠNG THÁI (Chỉ cho sửa khi chưa được xử lý)
        if ($khieuNai->TrangThai !== 'TiepNhan') {
            return response()->json([
                'success' => false, 
                'message' => 'Khiếu nại đang được Admin xử lý, bạn không thể chỉnh sửa lúc này!'
            ], 403);
        }

        // Validate nội dung mới
        $request->validate(['NoiDung' => 'required|string|max:2000']);

        $khieuNai->update([
            'NoiDung' => $request->NoiDung
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Cập nhật thành công!',
            'data' => $khieuNai
        ]);
    }

    /**
     * Xóa khiếu nại (DELETE /api/khieunai/{id})
     */
    public function destroy($id)
    {
        $khieuNai = KhieuNai::where('KhieuNaiID', $id)
                            ->where('TaiKhoanID', Auth::id())
                            ->first();

        if (!$khieuNai) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy khiếu nại!'], 404);
        }

        // 1. KIỂM TRA THỜI GIAN: 5 PHÚT
        $thoiGianTao = \Carbon\Carbon::parse($khieuNai->NgayTao);
        if (now()->diffInMinutes($thoiGianTao) > 5) {
            return response()->json([
                'success' => false, 
                'message' => 'Đã quá 5 phút, không thể xóa được nữa!'
            ], 403);
        }

        // 2. KIỂM TRA TRẠNG THÁI
        if ($khieuNai->TrangThai !== 'TiepNhan') {
            return response()->json([
                'success' => false, 
                'message' => 'Khiếu nại đang được Admin xử lý, bạn không thể xóa lúc này!'
            ], 403);
        }

        $khieuNai->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Đã xóa khiếu nại!'
        ]);
    }

    /**
     * Lấy danh sách khiếu nại (API: GET /api/khieunai)
     */
    public function index(Request $request)
    {
        $query = KhieuNai::with(['taiKhoan', 'lop', 'giaoDich'])
                    ->orderBy('NgayTao', 'desc');

        // Nếu bạn có dùng Auth thì có thể lọc theo tài khoản đang đăng nhập
        if (Auth::check()) {
            $query->where('TaiKhoanID', Auth::id());
        } elseif ($request->has('TaiKhoanID')) {
            $query->where('TaiKhoanID', $request->TaiKhoanID);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }
}
