<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiaoDich;
use App\Models\LopHocYeuCau; // Import Model Lớp học
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // <--- QUAN TRỌNG: Import DB Facade để sửa lỗi

class GiaoDichController extends Controller
{
    /**
     * Lưu một giao dịch mới và cập nhật trạng thái lớp học.
     */
    public function store(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'LopYeuCauID' => 'required|integer',
            'TaiKhoanID' => 'required|integer',
            'SoTien' => 'required|numeric',
            'LoaiGiaoDich' => 'required|string|max:50',
            'GhiChu' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Bắt đầu Transaction để đảm bảo dữ liệu toàn vẹn
        DB::beginTransaction(); 
        try {
            // 2. Tạo bản ghi giao dịch
            $giaoDich = GiaoDich::create([
                'LopYeuCauID' => $data['LopYeuCauID'],
                'TaiKhoanID' => $data['TaiKhoanID'],
                'SoTien' => $data['SoTien'],
                'LoaiGiaoDich' => $data['LoaiGiaoDich'],
                'GhiChu' => $data['GhiChu'] ?? null,
                'ThoiGian' => now(),
                'TrangThai' => 'Thành công',
                'MaGiaoDich' => 'TXN_' . time() . '_' . $data['TaiKhoanID']
            ]);

            // 3. Cập nhật trạng thái thanh toán cho lớp học
            $lopHoc = LopHocYeuCau::find($data['LopYeuCauID']);
            if ($lopHoc) {
                // Cập nhật cột TrangThaiThanhToan thành 'DaThanhToan'
                $lopHoc->TrangThaiThanhToan = 'DaThanhToan';
                $lopHoc->save();
            }

            // Xác nhận Transaction
            DB::commit();

            return response()->json([
                'message' => 'Giao dịch thành công',
                'data' => $giaoDich
            ], 201);

        } catch (\Exception $e) {
            // Hoàn tác nếu có lỗi
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi xử lý giao dịch: ' . $e->getMessage()
            ], 500);
        }
    }
}