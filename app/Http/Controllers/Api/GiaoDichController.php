<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaoDich;
use Illuminate\Support\Facades\Validator;

class GiaoDichController extends Controller
{
    /**
     * Lưu một giao dịch mới.
     */
    public function store(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào từ Flutter
        $validator = Validator::make($request->all(), [
            'LopYeuCauID' => 'required|integer', // |exists:LopHocYeuCau,LopYeuCauID (Nên thêm)
            'TaiKhoanID' => 'required|integer', // |exists:TaiKhoan,TaiKhoanID (Nên thêm)
            'SoTien' => 'required|numeric',
            'LoaiGiaoDich' => 'required|string|max:50',
            'GhiChu' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Lấy dữ liệu đã xác thực
        $data = $validator->validated();

        try {
            // 3. Tạo giao dịch
            $giaoDich = GiaoDich::create([
                'LopYeuCauID' => $data['LopYeuCauID'], // <-- SỬA LẠI TỪ 'Loai' THÀNH 'Lop'
                'TaiKhoanID' => $data['TaiKhoanID'],
                'SoTien' => $data['SoTien'],
                'LoaiGiaoDich' => $data['LoaiGiaoDich'], // <-- Cột này giờ sẽ hoạt động
                'GhiChu' => $data['GhiChu'] ?? null,
                'ThoiGian' => now(),
                'TrangThai' => 'Thành công',
                'MaGiaoDich' => 'TXN_' . time() . '_' . $data['TaiKhoanID']
            ]);

            // 4. Trả về kết quả thành công
            return response()->json([
                'message' => 'Giao dịch thành công',
                'data' => $giaoDich
            ], 201);

        } catch (\Exception $e) {
            // === SỬA ĐỔI: Trả về lỗi CSDL chi tiết ===
            // Xử lý nếu có lỗi khi lưu vào DB (ví dụ: sai Khóa Ngoại)
            return response()->json([
                'message' => $e->getMessage(), // Hiển thị lỗi thật
                'error' => $e->getMessage()
            ], 500);
            // ======================================
        }
    }
}