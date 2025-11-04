<?php
namespace App\Http\Controllers;

use App\Models\LichHoc;
use App\Models\LopHocYeuCau;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LichHocController extends Controller
{
    /**
     * Lấy lịch học theo lớp (CẬP NHẬT)
     */
    public function getLichHocTheoLop($lopYeuCauId): JsonResponse
    {
        try {
            $lopHoc = LopHocYeuCau::with(['giaSu', 'nguoiHoc'])->find($lopYeuCauId);
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            // Lấy tất cả lịch học, nhóm theo chuỗi lặp
            $lichHoc = LichHoc::where('LopYeuCauID', $lopYeuCauId)
                ->with(['lichHocCon' => function($q) {
                    $q->orderBy('NgayHoc', 'asc');
                }])
                ->where(function($q) {
                    $q->whereColumn('LichHocID', 'LichHocGocID')
                      ->orWhereNull('LichHocGocID');
                })
                ->orderBy('NgayHoc', 'asc')
                ->orderBy('ThoiGianBatDau', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'lop_hoc' => $lopHoc,
                    'lich_hoc' => $lichHoc
                ],
                'tong_so_buoi' => LichHoc::where('LopYeuCauID', $lopYeuCauId)->count(),
                'tong_so_chuoi' => $lichHoc->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo lịch học đơn lẻ (GIỮ NGUYÊN)
     */
    public function taoLichHocChoGiaSu(Request $request, $lopYeuCauId): JsonResponse
    {
        try {
            $lopHoc = LopHocYeuCau::find($lopYeuCauId);
            
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            $giasuId = auth()->user()->giasu->GiaSuID;
            
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

            // Kiểm tra trùng lịch
            if ($this->kiemTraTrungLich($giasuId, $validated['NgayHoc'], 
                $validated['ThoiGianBatDau'], $validated['ThoiGianKetThuc'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trùng lịch học. Vui lòng chọn thời gian khác.'
                ], 409);
            }

            $validated['LopYeuCauID'] = $lopYeuCauId;
            $validated['IsLapLai'] = false;

            $lichHoc = LichHoc::create($validated);
            $lichHoc->update(['LichHocGocID' => $lichHoc->LichHocID]);

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

    /**
     * Tạo lịch học với tính năng lặp lại hàng tuần (MỚI)
     */
    public function taoLichHocLapLai(Request $request, $lopYeuCauId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $lopHoc = LopHocYeuCau::with('giaSu')->find($lopYeuCauId);
            
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            $giasuId = auth()->user()->giasu->GiaSuID;
            
            if ($lopHoc->GiaSuID != $giasuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo lịch cho lớp này'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'ThoiGianBatDau' => 'required|date_format:H:i:s',
                'ThoiGianKetThuc' => 'required|date_format:H:i:s',
                'NgayHoc' => 'required|date',
                'DuongDan' => 'nullable|url',
                'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy',
                'LapLai' => 'required|boolean',
                'SoTuanLap' => 'required_if:LapLai,true|integer|min:1|max:52'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $lichHocGoc = null;
            $soBuoiTao = 0;
            $buoiHocTao = [];

            if ($validated['LapLai']) {
                // Tạo chuỗi lịch học lặp lại
                $soTuan = $validated['SoTuanLap'];
                $ngayHocGoc = Carbon::parse($validated['NgayHoc']);

                // Kiểm tra trùng lịch cho tất cả các buổi
                for ($i = 0; $i < $soTuan; $i++) {
                    $ngayHocMoi = $ngayHocGoc->copy()->addWeeks($i);
                    
                    if ($this->kiemTraTrungLich($giasuId, $ngayHocMoi->format('Y-m-d'), 
                        $validated['ThoiGianBatDau'], $validated['ThoiGianKetThuc'])) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Trùng lịch vào ngày {$ngayHocMoi->format('d/m/Y')}. Vui lòng chọn ngày khác."
                        ], 409);
                    }
                }

                // Tạo các buổi học
                for ($i = 0; $i < $soTuan; $i++) {
                    $ngayHocMoi = $ngayHocGoc->copy()->addWeeks($i);

                    $lichHocData = [
                        'LopYeuCauID' => $lopYeuCauId,
                        'ThoiGianBatDau' => $validated['ThoiGianBatDau'],
                        'ThoiGianKetThuc' => $validated['ThoiGianKetThuc'],
                        'NgayHoc' => $ngayHocMoi->format('Y-m-d'),
                        'DuongDan' => $validated['DuongDan'] ?? null,
                        'TrangThai' => $validated['TrangThai'] ?? 'SapToi',
                        'NgayTao' => now(),
                        'IsLapLai' => true
                    ];

                    // Buổi đầu tiên là buổi gốc
                    if ($i === 0) {
                        $lichHocGoc = LichHoc::create($lichHocData);
                        $lichHocData['LichHocGocID'] = $lichHocGoc->LichHocID;
                        $lichHocGoc->update(['LichHocGocID' => $lichHocGoc->LichHocID]);
                        $buoiHocTao[] = $lichHocGoc;
                    } else {
                        $lichHocData['LichHocGocID'] = $lichHocGoc->LichHocID;
                        $buoiHoc = LichHoc::create($lichHocData);
                        $buoiHocTao[] = $buoiHoc;
                    }

                    $soBuoiTao++;
                }
            } else {
                // Tạo lịch học đơn lẻ
                if ($this->kiemTraTrungLich($giasuId, $validated['NgayHoc'], 
                    $validated['ThoiGianBatDau'], $validated['ThoiGianKetThuc'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Trùng lịch học. Vui lòng chọn thời gian khác.'
                    ], 409);
                }

                $lichHocData = [
                    'LopYeuCauID' => $lopYeuCauId,
                    'ThoiGianBatDau' => $validated['ThoiGianBatDau'],
                    'ThoiGianKetThuc' => $validated['ThoiGianKetThuc'],
                    'NgayHoc' => $validated['NgayHoc'],
                    'DuongDan' => $validated['DuongDan'] ?? null,
                    'TrangThai' => $validated['TrangThai'] ?? 'SapToi',
                    'NgayTao' => now(),
                    'IsLapLai' => false,
                    'LichHocGocID' => null
                ];

                $lichHocGoc = LichHoc::create($lichHocData);
                $lichHocGoc->update(['LichHocGocID' => $lichHocGoc->LichHocID]);
                $soBuoiTao = 1;
                $buoiHocTao[] = $lichHocGoc;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['LapLai'] ? 
                    "Đã tạo {$soBuoiTao} buổi học lặp lại thành công" : 
                    "Tạo lịch học thành công",
                'data' => $buoiHocTao,
                'so_buoi_tao' => $soBuoiTao,
                'lop_yeu_cau' => $lopHoc
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật lịch học (GIỮ NGUYÊN)
     */
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

            $giasuId = auth()->user()->giasu->GiaSuID;
            
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

            // Kiểm tra trùng lịch khi cập nhật
            if (isset($validated['NgayHoc']) || isset($validated['ThoiGianBatDau']) || isset($validated['ThoiGianKetThuc'])) {
                $ngayHoc = $validated['NgayHoc'] ?? $lichHoc->NgayHoc;
                $thoiGianBatDau = $validated['ThoiGianBatDau'] ?? $lichHoc->ThoiGianBatDau;
                $thoiGianKetThuc = $validated['ThoiGianKetThuc'] ?? $lichHoc->ThoiGianKetThuc;
                
                if ($this->kiemTraTrungLich($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Trùng lịch học. Vui lòng chọn thời gian khác.'
                    ], 409);
                }
            }

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

    /**
     * Xóa lịch học (có hỏi xóa 1 buổi hay cả chuỗi) - MỚI
     */
    public function xoaLichHoc(Request $request, $lichHocId): JsonResponse
    {
        try {
            $lichHoc = LichHoc::with('lopHocYeuCau.giaSu')->find($lichHocId);
            
            if (!$lichHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch học không tồn tại'
                ], 404);
            }

            $giasuId = auth()->user()->giasu->GiaSuID;
            if ($lichHoc->lopHocYeuCau->GiaSuID != $giasuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa lịch học này'
                ], 403);
            }

            $xoaCaChuoi = filter_var($request->input('xoa_ca_chuoi', false), FILTER_VALIDATE_BOOLEAN);

            DB::beginTransaction();

            if ($xoaCaChuoi && $lichHoc->IsLapLai) {
                // Xóa cả chuỗi lịch học
                $lichHocGocId = $lichHoc->LichHocGocID ?: $lichHoc->LichHocID;
                $soBuoiXoa = LichHoc::where('LichHocGocID', $lichHocGocId)
                    ->orWhere('LichHocID', $lichHocGocId)
                    ->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Đã xóa {$soBuoiXoa} buổi học trong chuỗi"
                ]);
            } else {
                // Chỉ xóa 1 buổi
                $lichHoc->delete();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa buổi học thành công'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trùng lịch - MỚI
     */
    private function kiemTraTrungLich($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId = null): bool
    {
        $query = LichHoc::whereHas('lopHocYeuCau', function($q) use ($giasuId) {
                $q->where('GiaSuID', $giasuId);
            })
            ->where('NgayHoc', $ngayHoc)
            ->where('TrangThai', '!=', 'Huy')
            ->where(function($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                $q->whereBetween('ThoiGianBatDau', [$thoiGianBatDau, $thoiGianKetThuc])
                  ->orWhereBetween('ThoiGianKetThuc', [$thoiGianBatDau, $thoiGianKetThuc])
                  ->orWhere(function($q2) use ($thoiGianBatDau, $thoiGianKetThuc) {
                      $q2->where('ThoiGianBatDau', '<=', $thoiGianBatDau)
                         ->where('ThoiGianKetThuc', '>=', $thoiGianKetThuc);
                  });
            });

        if ($lichHocId) {
            $query->where('LichHocID', '!=', $lichHocId);
        }

        return $query->exists();
    }
    /**
 * Lấy lịch học theo người học (HIỂN THỊ CHO NGƯỜI HỌC)
 */
public function getLichHocTheoNguoiHoc(Request $request): JsonResponse
{
    try {
        $user = auth()->user();
        
        // Kiểm tra user có phải là người học không
        if (!$user->nguoiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải là người học'
            ], 403);
        }

        $nguoiHocId = $user->nguoiHoc->NguoiHocID;
        
        // Lấy các tham số filter từ request
        $trangThai = $request->input('trang_thai');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        // Lấy tất cả lớp học của người học
        $lopHocCuaNguoiHoc = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
            ->where('TrangThai', 'DangHoc') // Chỉ lấy lớp đang học
            ->pluck('LopYeuCauID');

        if ($lopHocCuaNguoiHoc->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Người học chưa có lớp học nào'
            ]);
        }

        // Query lịch học
        $query = LichHoc::whereIn('LopYeuCauID', $lopHocCuaNguoiHoc)
            ->with(['lopHocYeuCau' => function($q) {
                $q->with(['giaSu', 'monHoc', 'khoiLop']);
            }])
            ->orderBy('NgayHoc', 'asc')
            ->orderBy('ThoiGianBatDau', 'asc');

        // Filter theo trạng thái
        if ($trangThai) {
            $query->where('TrangThai', $trangThai);
        }

        // Filter theo khoảng ngày
        if ($tuNgay) {
            $query->where('NgayHoc', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->where('NgayHoc', '<=', $denNgay);
        }

        $lichHoc = $query->get();

        // Nhóm lịch học theo ngày
        $lichHocTheoNgay = $lichHoc->groupBy('NgayHoc')->map(function($items) {
            return $items->sortBy('ThoiGianBatDau')->values();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'lich_hoc' => $lichHoc,
                'lich_hoc_theo_ngay' => $lichHocTheoNgay,
                'thong_tin_nguoi_hoc' => $user->nguoiHoc
            ],
            'tong_so_buoi' => $lichHoc->count(),
            'tong_so_lop' => $lopHocCuaNguoiHoc->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Lấy lịch học theo gia sư (HIỂN THỊ CHO GIA SƯ)
 */
public function getLichHocTheoGiaSu(Request $request): JsonResponse
{
    try {
        $user = auth()->user();
        
        // Kiểm tra user có phải là gia sư không
        if (!$user->giasu) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải là gia sư'
            ], 403);
        }

        $giaSuId = $user->giasu->GiaSuID;
        
        // Lấy các tham số filter từ request
        $trangThai = $request->input('trang_thai');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        // Lấy tất cả lớp học của gia sư
        $lopHocCuaGiaSu = LopHocYeuCau::where('GiaSuID', $giaSuId)
            ->where('TrangThai', 'DangHoc') // Chỉ lấy lớp đang học
            ->pluck('LopYeuCauID');

        if ($lopHocCuaGiaSu->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Gia sư chưa có lớp học nào'
            ]);
        }

        // Query lịch học
        $query = LichHoc::whereIn('LopYeuCauID', $lopHocCuaGiaSu)
            ->with(['lopHocYeuCau' => function($q) {
                $q->with(['nguoiHoc', 'monHoc', 'khoiLop']);
            }])
            ->orderBy('NgayHoc', 'asc')
            ->orderBy('ThoiGianBatDau', 'asc');

        // Filter theo trạng thái
        if ($trangThai) {
            $query->where('TrangThai', $trangThai);
        }

        // Filter theo khoảng ngày
        if ($tuNgay) {
            $query->where('NgayHoc', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->where('NgayHoc', '<=', $denNgay);
        }

        $lichHoc = $query->get();

        // Nhóm lịch học theo ngày
        $lichHocTheoNgay = $lichHoc->groupBy('NgayHoc')->map(function($items) {
            return $items->sortBy('ThoiGianBatDau')->values();
        });

        // Thống kê
        $thongKe = [
            'sap_toi' => $lichHoc->where('TrangThai', 'SapToi')->count(),
            'dang_day' => $lichHoc->where('TrangThai', 'DangDay')->count(),
            'da_hoc' => $lichHoc->where('TrangThai', 'DaHoc')->count(),
            'huy' => $lichHoc->where('TrangThai', 'Huy')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'lich_hoc' => $lichHoc,
                'lich_hoc_theo_ngay' => $lichHocTheoNgay,
                'thong_tin_gia_su' => $user->giasu
                
            ],
            'thong_ke' => $thongKe,
            'tong_so_buoi' => $lichHoc->count(),
            'tong_so_lop' => $lopHocCuaGiaSu->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}
}