<?php
// app/Http/Controllers/LichHocController.php

namespace App\Http\Controllers;

use App\Models\LichHoc;
use App\Models\LopHocYeuCau;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class LichHocController extends Controller
{
   public function getLichHocTheoLop($lopYeuCauId): JsonResponse
{
    try {
        // Kiểm tra lớp học tồn tại
        $lopHoc = LopHocYeuCau::find($lopYeuCauId);
        if (!$lopHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học không tồn tại'
            ], 404);
        }

        // Lấy tất cả lịch học của lớp này
        $lichHoc = LichHoc::where('LopYeuCauID', $lopYeuCauId)
            ->orderBy('NgayHoc', 'asc')
            ->orderBy('ThoiGianBatDau', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'lop_hoc' => $lopHoc,
                'lich_hoc' => $lichHoc
            ],
            'tong_so_buoi' => $lichHoc->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}

public function taoLichHocChoGiaSu(Request $request, $lopYeuCauId): JsonResponse
{
    try {
        // Kiểm tra lớp học và quyền của gia sư
        $lopHoc = LopHocYeuCau::find($lopYeuCauId);
        
        if (!$lopHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học không tồn tại'
            ], 404);
        }

        // Lấy ID gia sư từ token (thực tế)
        $giasuId = auth()->user()->giasu->GiaSuID; // Giả sử có relationship
        
        // Kiểm tra gia sư có dạy lớp này không
        if ($lopHoc->GiaSuID != $giasuId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền tạo lịch cho lớp này'
            ], 403);
        }

        $validated = $request->validate([
            'ThoiGianBatDau' => 'required|date_format:H:i:s',
            'ThoiGianKetThuc' => 'required|date_format:H:i:s',
            'NgayHoc' => 'required|date',
            'DuongDan' => 'nullable|url',
            'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy'
        ]);

        // Thêm LopYeuCauID vào dữ liệu
        $validated['LopYeuCauID'] = $lopYeuCauId;

        $lichHoc = LichHoc::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo lịch học thành công',
            'data' => $lichHoc
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}

public function capNhatLichHocGiaSu(Request $request, $lichHocId): JsonResponse
{
    try {
        $lichHoc = LichHoc::with('lopHocYeuCau')->find($lichHocId);
        
        if (!$lichHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Lịch học không tồn tại'
            ], 404);
        }

        // Lấy ID gia sư từ token
        $giasuId = auth()->user()->giasu->GiaSuID;
        
        // Kiểm tra gia sư có dạy lớp này không
        if ($lichHoc->lopHocYeuCau->GiaSuID != $giasuId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền sửa lịch học này'
            ], 403);
        }

        $validated = $request->validate([
            'ThoiGianBatDau' => 'sometimes|required|date_format:H:i:s',
            'ThoiGianKetThuc' => 'sometimes|required|date_format:H:i:s',
            'NgayHoc' => 'sometimes|required|date',
            'DuongDan' => 'nullable|url',
            'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy'
        ]);

        $lichHoc->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật lịch học thành công',
            'data' => $lichHoc
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}
}