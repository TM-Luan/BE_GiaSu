<?php
namespace App\Http\Controllers\Api;

use App\Models\LichHoc;
use App\Models\LopHocYeuCau;
use App\Http\Resources\LichHocResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class LichHocController extends Controller
{
    /**
     * Tạo lịch học với tính năng lặp lại hàng tuần
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
                'NgayHoc' => 'required|date|after_or_equal:today',
                'DuongDan' => 'nullable|url',
                'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy',
                'LapLai' => 'required|boolean',
                'SoTuanLap' => 'required_if:LapLai,true|integer|min:1|max:52'
            ], [
                'ThoiGianBatDau.required' => 'Thời gian bắt đầu (HH:mm:ss) là bắt buộc.',
                'ThoiGianBatDau.date_format' => 'Thời gian bắt đầu phải có định dạng HH:mm:ss.',
                'ThoiGianKetThuc.required' => 'Thời gian kết thúc (HH:mm:ss) là bắt buộc.',
                'ThoiGianKetThuc.date_format' => 'Thời gian kết thúc phải có định dạng HH:mm:ss.',
                'NgayHoc.required' => 'Ngày học là bắt buộc.',
                'NgayHoc.date' => 'Ngày học phải là một ngày hợp lệ (YYYY-MM-DD).',
                'NgayHoc.after_or_equal' => 'Ngày học phải là ngày hôm nay hoặc sau đó.',
                'DuongDan.url' => 'Đường dẫn phải là một URL hợp lệ.',
                'TrangThai.in' => 'Trạng thái phải là một trong: DangDay, SapToi, DaHoc, Huy.',
                'LapLai.required' => 'Trường LapLai là bắt buộc.',
                'LapLai.boolean' => 'Trường LapLai phải là true hoặc false.',
                'SoTuanLap.required_if' => 'Số tuần lặp là bắt buộc khi LapLai là true.',
                'SoTuanLap.integer' => 'Số tuần lặp phải là một số nguyên.',
                'SoTuanLap.min' => 'Số tuần lặp phải ít nhất là 1.',
                'SoTuanLap.max' => 'Số tuần lặp không được vượt quá 52.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Kiểm tra thêm: Thời gian bắt đầu phải trước thời gian kết thúc
            $thoiGianBatDau = Carbon::createFromFormat('H:i:s', $validated['ThoiGianBatDau']);
            $thoiGianKetThuc = Carbon::createFromFormat('H:i:s', $validated['ThoiGianKetThuc']);
            
            if ($thoiGianBatDau->gte($thoiGianKetThuc)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời gian bắt đầu phải trước thời gian kết thúc.',
                    'errors' => [
                        'thoi_gian' => 'Thời gian bắt đầu (' . $validated['ThoiGianBatDau'] . ') phải trước thời gian kết thúc (' . $validated['ThoiGianKetThuc'] . ').'
                    ]
                ], 422);
            }

            // Kiểm tra thêm: Ngày học không được quá xa (không quá 1 năm)
            $ngayHoc = Carbon::parse($validated['NgayHoc']);
            if ($ngayHoc->diffInMonths(now()) > 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ngày học không được quá 1 năm từ hôm nay.',
                    'errors' => [
                        'NgayHoc' => 'Ngày học (' . $validated['NgayHoc'] . ') không được quá 12 tháng từ hôm nay.'
                    ]
                ], 422);
            }

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
                    $ngayDinhDang = $ngayHocMoi->format('d/m/Y');
                    
                    // Lấy thông tin lịch bị trùng (nếu có)
                    $lichTrung = $this->getLichHocTrung($giasuId, $ngayHocMoi->format('Y-m-d'), 
                        $validated['ThoiGianBatDau'], $validated['ThoiGianKetThuc']);
                    
                    if ($lichTrung) {
                        $tenMonHoc = $lichTrung->lopHocYeuCau->monHoc->TenMon ?? 'N/A';
                        $tenHocSinh = $lichTrung->lopHocYeuCau->nguoiHoc->HoTen ?? 'N/A';
                        
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Trùng lịch học - Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}: $tenMonHoc - Học sinh: $tenHocSinh",
                            'errors' => [
                                'trung_lich' => "Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}"
                            ]
                        ], 409);
                    }
                }

                // Tạo các buổi học
                for ($i = 0; $i < $soTuan; $i++) {
                    $ngayHocMoi = $ngayHocGoc->copy()->addWeeks($i);

                    $lichHocData = [
                        'LopYeuCauID' => $lopYeuCauId,
                        'TenLop' => $lopHoc->TenLop,
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
                    
                    // Lấy thông tin lịch bị trùng để hiển thị chi tiết
                    $lichTrung = $this->getLichHocTrung($giasuId, $validated['NgayHoc'], 
                        $validated['ThoiGianBatDau'], $validated['ThoiGianKetThuc']);
                    
                    $tenMonHoc = $lichTrung->lopHocYeuCau->monHoc->TenMon ?? 'N/A';
                    $tenHocSinh = $lichTrung->lopHocYeuCau->nguoiHoc->HoTen ?? 'N/A';
                    $ngayDinhDang = Carbon::parse($validated['NgayHoc'])->format('d/m/Y');
                    
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Trùng lịch học - Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}: $tenMonHoc - Học sinh: $tenHocSinh",
                        'errors' => [
                            'trung_lich' => "Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}"
                        ]
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
    
    // [HÀM ĐÃ NÂNG CẤP HOÀN TOÀN]
    public function taoNhieuLichHocTheoTuan(Request $request, $lopYeuCauId): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Lấy lớp học VÀ thời lượng mặc định
            $lopHoc = LopHocYeuCau::with('giaSu')->find($lopYeuCauId);
            
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            // Lấy thời lượng (ví dụ 90 phút) từ lớp học
            $thoiLuong = $lopHoc->ThoiLuong; 
            if (!$thoiLuong || $thoiLuong <= 0) {
                $thoiLuong = 90; // Mặc định 90 phút nếu lớp không có
            }

            $giasuId = auth()->user()->giasu->GiaSuID;
            
            if ($lopHoc->GiaSuID != $giasuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tạo lịch cho lớp này'
                ], 403);
            }

            // [SỬA] Validator hoàn toàn mới
            $validator = Validator::make($request->all(), [
                'ngay_bat_dau' => 'required|date',
                'so_tuan' => 'required|integer|min:1|max:52',
                'duong_dan' => 'nullable|string|max:1000',
                'trang_thai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy',

                // Yêu cầu một mảng "buoi_hoc_mau"
                'buoi_hoc_mau' => 'required|array|min:1',
                // Kiểm tra từng item trong mảng
                'buoi_hoc_mau.*.ngay_thu' => 'required|integer|min:0|max:6', // 0=CN, 1=T2,...
                'buoi_hoc_mau.*.thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            ]);
            // Lưu ý: Không cần 'thoi_gian_ket_thuc' vì chúng ta sẽ tự tính
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $ngayBatDau = Carbon::parse($validated['ngay_bat_dau']);
            $soTuan = $validated['so_tuan'];
            $buoiHocMau = $validated['buoi_hoc_mau']; // Mảng, ví dụ: [{ngay_thu: 1, thoi_gian_bat_dau: "19:00:00"}, {ngay_thu: 3, thoi_gian_bat_dau: "18:00:00"}]
            
            $buoiHocTao = [];
            $idGoc = null;

            // [SỬA] Lặp qua các buổi học mẫu (T2 19h, T4 18h)
            foreach ($buoiHocMau as $buoi) {
                
                $ngayThu = $buoi['ngay_thu'];
                $thoiGianBatDau = $buoi['thoi_gian_bat_dau'];
                
                // Tự động tính thời gian kết thúc
                $thoiGianKetThuc = Carbon::parse($thoiGianBatDau)
                                    ->addMinutes($thoiLuong)
                                    ->format('H:i:s');

                // 1. Tìm ngày học đầu tiên
                $ngayDauTien = $ngayBatDau->copy()->startOfWeek(Carbon::SUNDAY)->addDays($ngayThu);
                
                if ($ngayDauTien->isBefore($ngayBatDau, 'day')) {
                    $ngayDauTien->addWeek();
                }

                // 2. Lặp $soTuan
                for ($tuan = 0; $tuan < $soTuan; $tuan++) {
                    $ngayHoc = $ngayDauTien->copy()->addWeeks($tuan);

                    // Kiểm tra trùng lịch
                    if ($this->kiemTraTrungLich($giasuId, $ngayHoc->format('Y-m-d'), 
                        $thoiGianBatDau, $thoiGianKetThuc)) {
                        
                        // Lấy thông tin lịch bị trùng để hiển thị chi tiết
                        $lichTrung = $this->getLichHocTrung($giasuId, $ngayHoc->format('Y-m-d'), 
                            $thoiGianBatDau, $thoiGianKetThuc);
                        
                        $tenMonHoc = $lichTrung->lopHocYeuCau->monHoc->TenMon ?? 'N/A';
                        $tenHocSinh = $lichTrung->lopHocYeuCau->nguoiHoc->HoTen ?? 'N/A';
                        $ngayDinhDang = $ngayHoc->format('d/m/Y');
                        
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Trùng lịch học - Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}: $tenMonHoc - Học sinh: $tenHocSinh",
                            'errors' => [
                                'trung_lich' => "Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}"
                            ]
                        ], 409);
                    }

                    $lichHocData = [
                        'LopYeuCauID' => $lopYeuCauId,
                        'ThoiGianBatDau' => $thoiGianBatDau,
                        'ThoiGianKetThuc' => $thoiGianKetThuc, // Dùng thời gian đã tính
                        'NgayHoc' => $ngayHoc->format('Y-m-d'),
                        'DuongDan' => $validated['duong_dan'] ?? null,
                        'TrangThai' => $validated['trang_thai'] ?? 'SapToi',
                        'NgayTao' => now(),
                        'IsLapLai' => true,
                        'LichHocGocID' => $idGoc
                    ];
                    
                    $buoiHoc = LichHoc::create($lichHocData);

                    if ($idGoc === null) {
                        $idGoc = $buoiHoc->LichHocID;
                    }
                    
                    $buoiHoc->update(['LichHocGocID' => $idGoc]);
                    
                    $buoiHocTao[] = $buoiHoc;
                }
            }
            // [HẾT PHẦN SỬA]
            
            DB::commit();
            
            $soBuoiTao = count($buoiHocTao);

            if ($soBuoiTao == 0) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Không có buổi học nào được tạo. Vui lòng kiểm tra lại ngày bắt đầu.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Đã tạo {$soBuoiTao} buổi học thành công",
                'data' => $buoiHocTao,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server khi tạo lịch: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Cập nhật lịch học
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
                'NgayHoc' => 'sometimes|required|date|after_or_equal:today',
                'DuongDan' => 'nullable|url',
                'TrangThai' => 'nullable|in:DangDay,SapToi,DaHoc,Huy'
            ], [
                'ThoiGianBatDau.date_format' => 'Thời gian bắt đầu phải có định dạng HH:mm:ss.',
                'ThoiGianKetThuc.date_format' => 'Thời gian kết thúc phải có định dạng HH:mm:ss.',
                'NgayHoc.date' => 'Ngày học phải là một ngày hợp lệ (YYYY-MM-DD).',
                'NgayHoc.after_or_equal' => 'Ngày học phải là ngày hôm nay hoặc sau đó.',
                'DuongDan.url' => 'Đường dẫn phải là một URL hợp lệ.',
                'TrangThai.in' => 'Trạng thái phải là một trong: DangDay, SapToi, DaHoc, Huy.',
            ]);

            // Kiểm tra thêm: Nếu cập nhật thời gian, phải đảm bảo thời gian bắt đầu < thời gian kết thúc
            if (isset($validated['ThoiGianBatDau']) || isset($validated['ThoiGianKetThuc'])) {
                $thoiGianBatDau = $validated['ThoiGianBatDau'] ?? $lichHoc->ThoiGianBatDau;
                $thoiGianKetThuc = $validated['ThoiGianKetThuc'] ?? $lichHoc->ThoiGianKetThuc;
                
                $batDau = Carbon::createFromFormat('H:i:s', $thoiGianBatDau);
                $ketThuc = Carbon::createFromFormat('H:i:s', $thoiGianKetThuc);
                
                if ($batDau->gte($ketThuc)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Thời gian bắt đầu phải trước thời gian kết thúc.',
                        'errors' => [
                            'thoi_gian' => 'Thời gian bắt đầu (' . $thoiGianBatDau . ') phải trước thời gian kết thúc (' . $thoiGianKetThuc . ').'
                        ]
                    ], 422);
                }
            }

            // Kiểm tra trùng lịch khi cập nhật
            if (isset($validated['NgayHoc']) || isset($validated['ThoiGianBatDau']) || isset($validated['ThoiGianKetThuc'])) {
                $ngayHoc = $validated['NgayHoc'] ?? $lichHoc->NgayHoc;
                $thoiGianBatDau = $validated['ThoiGianBatDau'] ?? $lichHoc->ThoiGianBatDau;
                $thoiGianKetThuc = $validated['ThoiGianKetThuc'] ?? $lichHoc->ThoiGianKetThuc;
                
                if ($this->kiemTraTrungLich($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId)) {
                    // Lấy thông tin lịch bị trùng để hiển thị chi tiết
                    $lichTrung = $this->getLichHocTrung($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId);
                    
                    $tenMonHoc = $lichTrung->lopHocYeuCau->monHoc->TenMon ?? 'N/A';
                    $tenHocSinh = $lichTrung->lopHocYeuCau->nguoiHoc->HoTen ?? 'N/A';
                    $ngayDinhDang = Carbon::parse($ngayHoc)->format('d/m/Y');
                    
                    return response()->json([
                        'success' => false,
                        'message' => "Trùng lịch học - Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}: $tenMonHoc - Học sinh: $tenHocSinh",
                        'errors' => [
                            'trung_lich' => "Ngày $ngayDinhDang từ {$lichTrung->ThoiGianBatDau} đến {$lichTrung->ThoiGianKetThuc}"
                        ]
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
     * Xóa lịch học (có hỏi xóa 1 buổi hay cả chuỗi)
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
public function xoaTatCaLichHocTheoLop(Request $request, $lopYeuCauId): JsonResponse
    {
        try {
            $lopHoc = LopHocYeuCau::find($lopYeuCauId);
            
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            // Xác thực quyền: Chỉ gia sư của lớp mới được xóa
            $giasuId = auth()->user()->giasu->GiaSuID;
            if ($lopHoc->GiaSuID != $giasuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa lịch của lớp này'
                ], 403);
            }

            DB::beginTransaction();

            $soBuoiXoa = LichHoc::where('LopYeuCauID', $lopYeuCauId)->delete();

            DB::commit();

            if ($soBuoiXoa > 0) {
                 return response()->json([
                    'success' => true,
                    'message' => "Đã xóa thành công {$soBuoiXoa} buổi học của lớp"
                ]);
            } else {
                 return response()->json([
                    'success' => true,
                    'message' => 'Lớp này không có lịch học nào để xóa'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server khi xóa lịch: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Lấy lịch học theo tháng cho GIA SƯ
     */
    public function getLichHocTheoThangGiaSu(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->giasu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là gia sư.'
                ], 403);
            }

            $thang = $request->input('thang', date('m'));
            $nam = $request->input('nam', date('Y'));
            $lopYeuCauId = $request->input('lop_yeu_cau_id');

            $lopHocCuaGiaSu = $this->getLopHocByUser($user, $lopYeuCauId);

            if ($lopHocCuaGiaSu->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'lich_hoc_theo_ngay' => [],
                        'thong_ke_thang' => [],
                        'thang' => (int)$thang,
                        'nam' => (int)$nam
                    ],
                    'message' => 'Không có lịch học trong tháng này.'
                ]);
            }

            $lichHoc = $this->getLichHocTheoThang($lopHocCuaGiaSu, $thang, $nam);
            $lichHocTheoNgay = $this->groupLichHocByDay($lichHoc);
            $thongKeThang = $this->tinhThongKeThang($lichHoc);
            $lopHocTrongThang = $lichHoc->pluck('lop')->unique('LopYeuCauID')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'lich_hoc_theo_ngay' => $lichHocTheoNgay,
                    'thong_ke_thang' => $thongKeThang,
                    'lop_hoc_trong_thang' => $lopHocTrongThang,
                    'thang' => (int)$thang,
                    'nam' => (int)$nam
                ],
                'tong_so_buoi' => $lichHoc->count(),
                'tong_so_lop' => $lopHocTrongThang->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] Lấy summary lịch học (danh sách ngày có lịch) cho GIA SƯ
     */
    public function getLichHocSummaryGiaSu(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->giasu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là gia sư.'
                ], 403);
            }

            $thang = $request->input('thang', date('m'));
            $nam = $request->input('nam', date('Y'));

            $lopHocCuaGiaSu = $this->getLopHocByUser($user);

            if ($lopHocCuaGiaSu->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $ngayCoLich = $this->getNgayCoLichTheoThang($lopHocCuaGiaSu, $thang, $nam);

            return response()->json([
                'success' => true,
                'data' => $ngayCoLich
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] Lấy chi tiết lịch học theo 1 NGÀY cho GIA SƯ
     */
    public function getLichHocTheoNgayGiaSu(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->giasu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là gia sư.'
                ], 403);
            }

            $ngay = $request->input('ngay');

            if (!$ngay) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp tham số ngày (ngay).'
                ], 400);
            }

            $lopHocCuaGiaSu = $this->getLopHocByUser($user);

            if ($lopHocCuaGiaSu->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $lichHoc = $this->getLichHocTheoNgay($lopHocCuaGiaSu, $ngay);

            return response()->json([
                'success' => true,
                'data' => LichHocResource::collection($lichHoc)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy lịch học theo tháng cho NGƯỜI HỌC
     */
    public function getLichHocTheoThangNguoiHoc(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $thang = $request->input('thang', date('m'));
            $nam = $request->input('nam', date('Y'));
            $lopYeuCauId = $request->input('lop_yeu_cau_id');

            $lopHocCuaNguoiHoc = $this->getLopHocByUser($user, $lopYeuCauId);

            if ($lopHocCuaNguoiHoc->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'lich_hoc_theo_ngay' => [],
                        'thong_ke_thang' => [],
                        'thang' => (int)$thang,
                        'nam' => (int)$nam
                    ],
                    'message' => 'Không có lịch học trong tháng này.'
                ]);
            }

            $lichHoc = $this->getLichHocTheoThang($lopHocCuaNguoiHoc, $thang, $nam);
            $lichHocTheoNgay = $this->groupLichHocByDay($lichHoc);
            $thongKeThang = $this->tinhThongKeThang($lichHoc);
            $lopHocTrongThang = $lichHoc->pluck('lop')->unique('LopYeuCauID')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'lich_hoc_theo_ngay' => $lichHocTheoNgay,
                    'thong_ke_thang' => $thongKeThang,
                    'lop_hoc_trong_thang' => $lopHocTrongThang,
                    'thang' => (int)$thang,
                    'nam' => (int)$nam
                ],
                'tong_so_buoi' => $lichHoc->count(),
                'tong_so_lop' => $lopHocTrongThang->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] Lấy summary lịch học (danh sách ngày có lịch) cho NGƯỜI HỌC
     */
    public function getLichHocSummaryNguoiHoc(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $thang = $request->input('thang', date('m'));
            $nam = $request->input('nam', date('Y'));

            $lopHocCuaNguoiHoc = $this->getLopHocByUser($user);

            if ($lopHocCuaNguoiHoc->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $ngayCoLich = $this->getNgayCoLichTheoThang($lopHocCuaNguoiHoc, $thang, $nam);

            return response()->json([
                'success' => true,
                'data' => $ngayCoLich
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] Lấy chi tiết lịch học theo 1 NGÀY cho NGƯỜI HỌC
     */
    public function getLichHocTheoNgayNguoiHoc(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->nguoiHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải là người học.'
                ], 403);
            }

            $ngay = $request->input('ngay');

            if (!$ngay) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp tham số ngày (ngay).'
                ], 400);
            }

            $lopHocCuaNguoiHoc = $this->getLopHocByUser($user);

            if ($lopHocCuaNguoiHoc->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $lichHoc = $this->getLichHocTheoNgay($lopHocCuaNguoiHoc, $ngay);

            return response()->json([
                'success' => true,
                'data' => LichHocResource::collection($lichHoc)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy lịch học theo lớp và tháng
     */
    public function getLichHocTheoLopVaThang($lopYeuCauId, Request $request): JsonResponse
    {
        try {
            $lopHoc = LopHocYeuCau::with(['giaSu', 'nguoiHoc'])->find($lopYeuCauId);
            
            if (!$lopHoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học không tồn tại'
                ], 404);
            }

            // Lấy tham số tháng, năm
            $thang = $request->input('thang', date('m'));
            $nam = $request->input('nam', date('Y'));

            // Lấy lịch học theo lớp và tháng
            $lichHoc = LichHoc::where('LopYeuCauID', $lopYeuCauId)
                ->with(['lichHocCon' => function($q) {
                    $q->orderBy('NgayHoc', 'asc');
                }])
                ->where(function($q) {
                    $q->whereColumn('LichHocID', 'LichHocGocID')
                      ->orWhereNull('LichHocGocID');
                })
                ->whereYear('NgayHoc', $nam)
                ->whereMonth('NgayHoc', $thang)
                ->orderBy('NgayHoc', 'asc')
                ->orderBy('ThoiGianBatDau', 'asc')
                ->get();

            // Gom nhóm lịch học theo ngày
            $lichHocTheoNgay = $lichHoc->groupBy('NgayHoc')->map(function($items) {
                return $items->sortBy('ThoiGianBatDau')->values();
            });

            // Thống kê theo tháng
            $thongKeThang = [
                'tong_so_buoi' => LichHoc::where('LopYeuCauID', $lopYeuCauId)
                                    ->whereYear('NgayHoc', $nam)
                                    ->whereMonth('NgayHoc', $thang)
                                    ->count(),
                'sap_toi' => LichHoc::where('LopYeuCauID', $lopYeuCauId)
                                ->whereYear('NgayHoc', $nam)
                                ->whereMonth('NgayHoc', $thang)
                                ->where('TrangThai', 'SapToi')
                                ->count(),
                'dang_day' => LichHoc::where('LopYeuCauID', $lopYeuCauId)
                                ->whereYear('NgayHoc', $nam)
                                ->whereMonth('NgayHoc', $thang)
                                ->where('TrangThai', 'DangDay')
                                ->count(),
                'da_hoc' => LichHoc::where('LopYeuCauID', $lopYeuCauId)
                                ->whereYear('NgayHoc', $nam)
                                ->whereMonth('NgayHoc', $thang)
                                ->where('TrangThai', 'DaHoc')
                                ->count(),
                'huy' => LichHoc::where('LopYeuCauID', $lopYeuCauId)
                            ->whereYear('NgayHoc', $nam)
                            ->whereMonth('NgayHoc', $thang)
                            ->where('TrangThai', 'Huy')
                            ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'lop_hoc' => $lopHoc,
                    'lich_hoc_theo_ngay' => $lichHocTheoNgay,
                    'thong_ke_thang' => $thongKeThang,
                    'thang' => (int)$thang,
                    'nam' => (int)$nam
                ],
                'tong_so_buoi' => $thongKeThang['tong_so_buoi']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách lớp theo loại người dùng (GiaSu hoặc NguoiHoc)
     */
    private function getLopHocByUser($user, $lopYeuCauId = null)
    {
        if ($user->giasu) {
            $query = LopHocYeuCau::where('GiaSuID', $user->giasu->GiaSuID);
        } elseif ($user->nguoiHoc) {
            $query = LopHocYeuCau::where('NguoiHocID', $user->nguoiHoc->NguoiHocID);
        } else {
            return null;
        }

        $query->where('TrangThai', 'DangHoc');

        if ($lopYeuCauId) {
            $query->where('LopYeuCauID', $lopYeuCauId);
        }

        return $query->pluck('LopYeuCauID');
    }

    /**
     * Eager loading relationships cho lịch học
     */
    private function getEagerLoadingRelations()
    {
        return [
            'lop' => function ($q) {
                $q->with([
                    'nguoiHoc' => function($q2) {
                        $q2->with('taiKhoan');
                    },
                    'giaSu' => function($q2) {
                        $q2->with('taiKhoan');
                    },
                    'monHoc',
                    'khoiLop',
                    'doiTuong',
                    'thoiGianDay'
                ]);
            }
        ];
    }

    /**
     * Lấy lịch học theo tháng (dùng chung cho GiaSu và NguoiHoc)
     */
    private function getLichHocTheoThang($lopIds, $thang, $nam)
    {
        return LichHoc::whereIn('LopYeuCauID', $lopIds)
            ->with($this->getEagerLoadingRelations())
            ->whereYear('NgayHoc', $nam)
            ->whereMonth('NgayHoc', $thang)
            ->orderBy('NgayHoc', 'asc')
            ->orderBy('ThoiGianBatDau', 'asc')
            ->get();
    }

    /**
     * Tính thống kê theo tháng
     */
    private function tinhThongKeThang($lichHoc)
    {
        return [
            'tong_so_buoi' => $lichHoc->count(),
            'sap_toi' => $lichHoc->where('TrangThai', 'SapToi')->count(),
            'dang_day' => $lichHoc->where('TrangThai', 'DangDay')->count(),
            'da_hoc' => $lichHoc->where('TrangThai', 'DaHoc')->count(),
            'huy' => $lichHoc->where('TrangThai', 'Huy')->count(),
        ];
    }

    /**
     * Gom nhóm lịch học theo ngày với Resource
     */
    private function groupLichHocByDay($lichHoc)
    {
        return $lichHoc->groupBy('NgayHoc')->map(function($items) {
            return LichHocResource::collection($items->sortBy('ThoiGianBatDau')->values())->resolve();
        });
    }

    /**
     * Lấy danh sách ngày có lịch theo tháng
     */
    private function getNgayCoLichTheoThang($lopIds, $thang, $nam)
    {
        return LichHoc::whereIn('LopYeuCauID', $lopIds)
            ->whereYear('NgayHoc', $nam)
            ->whereMonth('NgayHoc', $thang)
            ->where('TrangThai', '!=', 'Huy')
            ->selectRaw('DISTINCT DATE(NgayHoc) as ngay')
            ->pluck('ngay')
            ->toArray();
    }

    /**
     * Lấy lịch học theo ngày (dùng chung cho GiaSu và NguoiHoc)
     */
    private function getLichHocTheoNgay($lopIds, $ngay)
    {
        return LichHoc::whereIn('LopYeuCauID', $lopIds)
            ->with($this->getEagerLoadingRelations())
            ->whereDate('NgayHoc', $ngay)
            ->orderBy('ThoiGianBatDau', 'asc')
            ->get();
    }

    /**
     * Kiểm tra trùng lịch
     */
    private function kiemTraTrungLich($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId = null): bool
    {
        $query = LichHoc::whereHas('lopHocYeuCau', function($q) use ($giasuId) {
                $q->where('GiaSuID', $giasuId);
            })
            ->where('NgayHoc', $ngayHoc)
            ->where('TrangThai', '!=', 'Huy')
            ->where(function($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                $q->where('ThoiGianBatDau', '<', $thoiGianKetThuc)
                  ->where('ThoiGianKetThuc', '>', $thoiGianBatDau);
            });

        if ($lichHocId) {
            $query->where('LichHocID', '!=', $lichHocId);
        }

        return $query->exists();
    }

    /**
     * Lấy thông tin lịch bị trùng (dùng để hiển thị chi tiết lỗi)
     */
    private function getLichHocTrung($giasuId, $ngayHoc, $thoiGianBatDau, $thoiGianKetThuc, $lichHocId = null)
    {
        $query = LichHoc::with([
                'lopHocYeuCau' => function($q) {
                    $q->with(['monHoc', 'nguoiHoc']);
                }
            ])
            ->whereHas('lopHocYeuCau', function($q) use ($giasuId) {
                $q->where('GiaSuID', $giasuId);
            })
            ->where('NgayHoc', $ngayHoc)
            ->where('TrangThai', '!=', 'Huy')
            ->where(function($q) use ($thoiGianBatDau, $thoiGianKetThuc) {
                $q->where('ThoiGianBatDau', '<', $thoiGianKetThuc)
                  ->where('ThoiGianKetThuc', '>', $thoiGianBatDau);
            });

        if ($lichHocId) {
            $query->where('LichHocID', '!=', $lichHocId);
        }

        return $query->first();
    }
}