<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\LopHocYeuCau;
use App\Models\GiaSu;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DanhGiaController extends Controller
{
    /**
     * Tạo hoặc cập nhật đánh giá cho gia sư
     * Người học chỉ có thể đánh giá gia sư mà họ đã/đang học
     */
    public function taoDanhGia(Request $request): JsonResponse
    {
        try {
            /** @var TaiKhoan $user */
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
                // Kiểm tra xem đã sửa chưa (LanSua >= 1)
                $lanSua = $danhGiaExists->LanSua ?? 0;
                
                if ($lanSua >= 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã chỉnh sửa đánh giá này rồi. Mỗi học viên chỉ được sửa đánh giá 1 lần duy nhất.'
                    ], 403);
                }

                // Cập nhật đánh giá cũ (lần đầu sửa)
                $danhGiaExists->update([
                    'DiemSo' => $request->diem_so,
                    'BinhLuan' => $request->binh_luan,
                    'NgayDanhGia' => now(),
                    'LanSua' => $lanSua + 1, // Tăng số lần sửa
                ]);
                $danhGia = $danhGiaExists;
                $action = 'updated';
            } else {
                // Tạo đánh giá mới
                $danhGia = DanhGia::create([
                    'LopYeuCauID' => $lopHoc->LopYeuCauID,
                    'TaiKhoanID' => $user->TaiKhoanID,
                    'DiemSo' => $request->diem_so,
                    'BinhLuan' => $request->binh_luan,
                    'NgayDanhGia' => now(),
                    'LanSua' => 0, // Lần đầu tạo = 0
                ]);
                $action = 'created';
            }

            DB::commit();

            // Load lại với quan hệ
            $danhGia->load(['lop', 'taiKhoan']);

            return response()->json([
                'success' => true,
                'message' => $action === 'created' 
                    ? 'Đánh giá thành công!' 
                    : 'Cập nhật đánh giá thành công!',
                'data' => $danhGia
            ], $action === 'created' ? 201 : 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách đánh giá của một gia sư
     */
    public function getDanhGiaGiaSu($giaSuId): JsonResponse
    {
        try {
            $giaSu = GiaSu::find($giaSuId);
            
            if (!$giaSu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gia sư không tồn tại'
                ], 404);
            }

            // Lấy tất cả đánh giá của gia sư
            $danhGiaList = DanhGia::whereHas('lop', function($q) use ($giaSuId) {
                    $q->where('GiaSuID', $giaSuId);
                })
                ->with(['taiKhoan', 'lop.nguoiHoc'])
                ->orderBy('NgayDanhGia', 'desc')
                ->get();

            // Tính điểm trung bình
            $diemTrungBinh = $danhGiaList->avg('DiemSo');
            $tongSoDanhGia = $danhGiaList->count();

            // Phân bố đánh giá theo số sao
            $phanBoSao = [];
            for ($i = 1; $i <= 5; $i++) {
                $phanBoSao[$i] = $danhGiaList->where('DiemSo', $i)->count();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'danh_gia_list' => $danhGiaList,
                    'diem_trung_binh' => round($diemTrungBinh, 1),
                    'tong_so_danh_gia' => $tongSoDanhGia,
                    'phan_bo_sao' => $phanBoSao,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra người học đã đánh giá gia sư chưa
     */
    public function kiemTraDaDanhGia($giaSuId): JsonResponse
    {
        try {
            /** @var TaiKhoan $user */
            $user = Auth::user();

            if (!$user || !$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $nguoiHocId = $user->nguoiHoc->NguoiHocID;

            // Tìm lớp học
            $lopHoc = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
                ->where('GiaSuID', $giaSuId)
                ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
                ->first();

            if (!$lopHoc) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'co_the_danh_gia' => false,
                        'da_danh_gia' => false,
                        'danh_gia' => null,
                        'message' => 'Bạn chưa học với gia sư này.'
                    ]
                ]);
            }

            // Kiểm tra đã đánh giá chưa
            $danhGia = DanhGia::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('TaiKhoanID', $user->TaiKhoanID)
                ->with(['lop', 'taiKhoan'])
                ->first();

            // Kiểm tra đã sửa chưa (dựa vào cột LanSua)
            $daSua = false;
            if ($danhGia) {
                $lanSua = $danhGia->LanSua ?? 0;
                $daSua = $lanSua >= 1;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'co_the_danh_gia' => true,
                    'da_danh_gia' => $danhGia !== null,
                    'da_sua' => $daSua,
                    'danh_gia' => $danhGia,
                    'lop_hoc_id' => $lopHoc->LopYeuCauID,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa đánh giá của người học
     */
    public function xoaDanhGia($danhGiaId): JsonResponse
    {
        try {
            /** @var TaiKhoan $user */
            $user = Auth::user();

            if (!$user || !$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $danhGia = DanhGia::find($danhGiaId);

            if (!$danhGia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đánh giá không tồn tại'
                ], 404);
            }

            // Kiểm tra quyền xóa
            if ($danhGia->TaiKhoanID !== $user->TaiKhoanID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đánh giá này'
                ], 403);
            }

            $danhGia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đánh giá thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
