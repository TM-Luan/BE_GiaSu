<?php

namespace App\Http\Controllers\Api;

use App\Models\DanhGia;
use App\Models\LopHocYeuCau;
use App\Models\GiaSu;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class DanhGiaController extends Controller
{
    //=================================================================
    // HÀM HELPER (PRIVATE)
    //=================================================================

    /**
     * Hàm helper private để lấy chi tiết đánh giá của một gia sư
     * Được sử dụng bởi cả public và auth-gia-su
     */
    private function layThongTinDanhGia($giaSuId): array
    {
        $giaSu = GiaSu::find($giaSuId);

        if (!$giaSu) {
            return [
                'success' => false,
                'message' => 'Gia sư không tồn tại',
                'status_code' => 404
            ];
        }

        // Lấy tất cả đánh giá của gia sư
        $danhGiaList = DanhGia::whereHas('lop', function ($q) use ($giaSuId) {
            $q->where('GiaSuID', $giaSuId);
        })
            ->with([
                'taiKhoan' => function ($q) {
                    // Chỉ lấy thông tin public của tài khoản
                    $q->select('TaiKhoanID', 'Email'); // <--- SỬA THÀNH DÒNG NÀY
                },
                'lop.nguoiHoc'
            ])
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

        return [
            'success' => true,
            'status_code' => 200,
            'data' => [
                'danh_gia_list' => $danhGiaList,
                'diem_trung_binh' => round($diemTrungBinh, 1),
                'tong_so_danh_gia' => $tongSoDanhGia,
                'phan_bo_sao' => $phanBoSao,
            ]
        ];
    }

    //=================================================================
    // PHƯƠNG THỨC CÔNG KHAI (PUBLIC)
    // (Dùng cho trang profile gia sư, ai cũng xem được)
    //=================================================================

    /**
     * Lấy danh sách đánh giá công khai của một gia sư
     */
    public function getDanhGiaCongKhaiCuaGiaSu($giaSuId): JsonResponse
    {
        try {
            $result = $this->layThongTinDanhGia($giaSuId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], $result['status_code']);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }


    //=================================================================
    // PHƯƠNG THỨC CHO GIA SƯ (AUTH)
    //=================================================================

    /**
     * [GIA SƯ] Lấy danh sách đánh giá của chính mình (đã đăng nhập)
     */
    public function getDanhGiaCuaToiGiaSu(Request $request): JsonResponse
    {
        try {
            /** @var TaiKhoan $user */
            $user = Auth::user();

            if (!$user || !$user->giasu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là gia sư.'
                ], 403);
            }

            $giaSuId = $user->giasu->GiaSuID;

            $result = $this->layThongTinDanhGia($giaSuId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], $result['status_code']);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }


    //=================================================================
    // PHƯƠNG THỨC CHO NGƯỜI HỌC (AUTH)
    //=================================================================

    /**
     * [NGƯỜI HỌC] Tạo hoặc cập nhật đánh giá cho gia sư
     * Người học chỉ có thể đánh giá gia sư mà họ đã/đang học
     * Chỉ được phép cập nhật 1 lần duy nhất.
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

            // Kiểm tra đã có đánh giá chưa (dựa trên Lớp và Tài Khoản)
            $danhGiaExists = DanhGia::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                ->where('TaiKhoanID', $user->TaiKhoanID)
                ->first();

            DB::beginTransaction();

            if ($danhGiaExists) {
                // --- CẬP NHẬT ĐÁNH GIÁ ---
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
                // --- TẠO ĐÁNH GIÁ MỚI ---
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
     * [NGƯỜI HỌC] Kiểm tra xem mình đã đánh giá một gia sư cụ thể chưa
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

            // Tìm lớp học (đã/đang học) với gia sư này
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

            // Kiểm tra đã đánh giá cho lớp này chưa
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
     * [NGƯỜI HỌC] Lấy danh sách tất cả đánh giá của chính mình
     */
    public function getDanhGiaCuaToiNguoiHoc(Request $request): JsonResponse
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

            $danhGiaList = DanhGia::where('TaiKhoanID', $user->TaiKhoanID)
                ->with([
                    'lop' => function ($q) {
                        $q->with([
                            'giaSu' => function ($q2) {
                                $q2->with('taiKhoan'); // Lấy thông tin tài khoản của gia sư
                            },
                            'monHoc',
                            'khoiLop'
                        ]);
                    }
                ])
                ->orderBy('NgayDanhGia', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $danhGiaList,
                'total' => $danhGiaList->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * [NGƯỜI HỌC] Xóa đánh giá của chính mình
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