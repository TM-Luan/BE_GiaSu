<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\LopHocYeuCau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DanhGiaWebController extends Controller
{
    /**
     * Kiểm tra xem người học đã đánh giá gia sư chưa
     */
    public function kiemTraDanhGia($giaSuId)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $nguoiHocId = $user->nguoiHoc->NguoiHocID;

            // Tìm lớp học đang dạy hoặc đã hoàn thành với gia sư này
            $lopHoc = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
                ->where('GiaSuID', $giaSuId)
                ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'data' => null
                ]);
            }

            // Kiểm tra đã đánh giá cho lớp này chưa
            $danhGia = DanhGia::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('TaiKhoanID', $user->TaiKhoanID)
                ->first();

            if (!$danhGia) {
                return response()->json([
                    'success' => true,
                    'data' => null // Chưa đánh giá, cho phép đánh giá
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $danhGia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo hoặc cập nhật đánh giá
     */
    public function taoDanhGia(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $nguoiHocId = $user->nguoiHoc->NguoiHocID;

            // Validate
            $validator = Validator::make($request->all(), [
                'gia_su_id' => 'required|integer|exists:GiaSu,GiaSuID',
                'diem_so' => 'required|numeric|min:1|max:5',
                'binh_luan' => 'nullable|string|max:1000',
            ], [
                'gia_su_id.required' => 'Vui lòng chọn gia sư.',
                'gia_su_id.exists' => 'Gia sư không tồn tại.',
                'diem_so.required' => 'Vui lòng chọn số sao đánh giá.',
                'diem_so.min' => 'Đánh giá phải từ 1 đến 5 sao.',
                'diem_so.max' => 'Đánh giá phải từ 1 đến 5 sao.',
                'binh_luan.max' => 'Bình luận không được vượt quá 1000 ký tự.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $giaSuId = $request->gia_su_id;

            // Kiểm tra người học có đang học hoặc đã học với gia sư này không
            $lopHoc = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
                ->where('GiaSuID', $giaSuId)
                ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có thể đánh giá gia sư mà bạn đã hoặc đang học.'
                ], 403);
            }

            // Kiểm tra đã có đánh giá chưa
            $danhGiaExists = DanhGia::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('TaiKhoanID', $user->TaiKhoanID)
                ->first();

            DB::beginTransaction();

            if ($danhGiaExists) {
                // Cập nhật đánh giá
                $lanSua = $danhGiaExists->LanSua ?? 0;

                if ($lanSua >= 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã chỉnh sửa đánh giá này rồi. Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất.'
                    ], 403);
                }

                $danhGiaExists->update([
                    'DiemSo' => $request->diem_so,
                    'BinhLuan' => $request->binh_luan,
                    'NgayDanhGia' => now(),
                    'LanSua' => $lanSua + 1,
                ]);
                
                $message = 'Cập nhật đánh giá thành công!';
            } else {
                // Tạo đánh giá mới
                DanhGia::create([
                    'LopYeuCauID' => $lopHoc->LopYeuCauID,
                    'TaiKhoanID' => $user->TaiKhoanID,
                    'DiemSo' => $request->diem_so,
                    'BinhLuan' => $request->binh_luan,
                    'NgayDanhGia' => now(),
                    'LanSua' => 0,
                ]);
                
                $message = 'Đánh giá thành công!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê đánh giá của gia sư
     */
    public function getRatingStats($giaSuId)
    {
        try {
            $stats = \App\Models\DanhGia::whereHas('lop', function ($q) use ($giaSuId) {
                $q->where('GiaSuID', $giaSuId);
            })->selectRaw('
                ROUND(AVG(DiemSo), 1) as diem_trung_binh,
                COUNT(*) as tong_so_danh_gia
            ')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'average' => $stats->diem_trung_binh ?? 0,
                    'count' => $stats->tong_so_danh_gia ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
