<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhieuNai;
use Illuminate\Support\Facades\Auth;

class KhieuNaiController extends Controller
{
    /**
     * Lưu khiếu nại mới (API: POST /api/khieunai)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'NoiDung' => 'required|string|max:2000',
            'LopYeuCauID' => 'nullable|integer|exists:lophocyeucau,LopYeuCauID',
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
