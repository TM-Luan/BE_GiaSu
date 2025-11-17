<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LopHocYeuCau;
use App\Models\LichHoc;

class LichHocWebController extends Controller
{
    /**
     * Hiển thị trang lịch học (Highlight màu chữ)
     */
    public function index()
    {
        // 1. Lấy ID Người học
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 2. Lấy ID các lớp
        $lopHocIds = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
                            ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
                            ->pluck('LopYeuCauID');

        // 3. Lấy các buổi học
        $lichHocEvents = LichHoc::whereIn('LopYeuCauID', $lopHocIds)
                            ->with('lop.monHoc', 'lop.giaSu', 'lop.khoiLop')
                            ->get();

        // 4. [LOGIC MÀU MỚI] Bảng màu chỉ chứa MÀU CHỮ
        $colorPalette = [
            '#5B21B6', // Tím
            '#B45309', // Cam/Vàng đậm
            '#065F46', // Xanh lá
            '#1E3A8A', // Xanh dương
            '#9D174D', // Hồng
            '#991B1B', // Đỏ
            '#0F766E', // Teal
        ];
        
        $lopColorMap = [];
        $colorIndex = 0;
        foreach ($lopHocIds as $lopId) {
            $lopColorMap[$lopId] = $colorPalette[$colorIndex % count($colorPalette)];
            $colorIndex++;
        }

        // 5. Định dạng dữ liệu
        $events = [];
        foreach ($lichHocEvents as $lich) {
            $startDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianBatDau;
            $endDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianKetThuc;

            // Lấy màu chữ theo lớp
            $textColor = $lopColorMap[$lich->LopYeuCauID] ?? $colorPalette[0];
            
            $trangThaiText = 'Sắp diễn ra';
            if ($lich->TrangThai == 'DaHoanThanh') {
                $trangThaiText = 'Đã hoàn thành';
                $textColor = '#6B7280'; // Màu xám cho chữ
            }

            $events[] = [
                'id' => $lich->LichHocID,
                'title' => $lich->lop->monHoc->TenMon ?? 'Lịch học',
                'start' => $startDateTime,
                'end' => $endDateTime,
                
                // [THAY ĐỔI QUAN TRỌNG]
                // Chỉ gán 'textColor'. Bỏ 'borderColor' và 'backgroundColor'
                'textColor' => $textColor, 

                'extendedProps' => [
                    'monHoc' => ($lich->lop->monHoc->TenMon ?? 'N/A') . ' ' . ($lich->lop->khoiLop->BacHoc ?? ''),
                    'giaSuTen' => $lich->lop->giaSu->HoTen ?? 'Chưa có',
                    'hinhThuc' => $lich->lop->HinhThuc, // 'Online' hoặc 'Offline'
                    'trangThai' => $trangThaiText,
                    'duongDan' => $lich->DuongDan, // Link học
                    'thoiGianBatDau' => date('H:i', strtotime($lich->ThoiGianBatDau)), // 18:00
                ]
            ];
        }

        // 6. [CACHE BUSTING] Đổi tên biến trả về
        return view('nguoihoc.lich-hoc-index', [
            'calendarDataJson' => json_encode($events) // Đổi tên từ 'eventsJson'
        ]);
    }
    public function showScheduleForClass($id)
    {
        // 1. Lấy ID Người học
        $nguoiHocId = Auth::user()->nguoiHoc->NguoiHocID;

        // 2. Lấy lớp học và kiểm tra quyền sở hữu
        $lopHoc = LopHocYeuCau::where('LopYeuCauID', $id)
                            ->where('NguoiHocID', $nguoiHocId)
                            ->with('monHoc', 'khoiLop', 'giaSu')
                            ->firstOrFail(); // Sẽ 404 nếu không tìm thấy hoặc không sở hữu

        // 3. Lấy các buổi học CHỈ CỦA LỚP NÀY
        $lichHocEvents = LichHoc::where('LopYeuCauID', $lopHoc->LopYeuCauID)
                            ->with('lop.monHoc', 'lop.giaSu', 'lop.khoiLop') 
                            ->get();

        // 4. Logic màu sắc (chỉ dùng 1 màu cho lớp này)
        $colorPalette = [
            '#5B21B6', // Tím
            '#1E3A8A', // Xanh dương
            '#065F46', // Xanh lá
            '#B45309', // Vàng
            '#9D174D', // Hồng
            '#991B1B', // Đỏ
        ];
        // Chọn một màu dựa trên ID lớp
        $color = $colorPalette[$lopHoc->LopYeuCauID % count($colorPalette)];

        // 5. Định dạng dữ liệu
        $events = [];
        foreach ($lichHocEvents as $lich) {
            $startDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianBatDau;
            $endDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianKetThuc;

            $textColor = $color; // Dùng màu đã chọn
            $trangThaiText = 'Sắp diễn ra';
            if ($lich->TrangThai == 'DaHoanThanh') {
                $trangThaiText = 'Đã hoàn thành';
                $textColor = '#6B7280'; // Ghi đè màu xám cho chữ
            }

            $events[] = [
                'id' => $lich->LichHocID,
                'title' => $lich->lop->monHoc->TenMon ?? 'Lịch học',
                'start' => $startDateTime,
                'end' => $endDateTime,
                'textColor' => $textColor, 
                'extendedProps' => [
                    'monHoc' => ($lich->lop->monHoc->TenMon ?? 'N/A') . ' ' . ($lich->lop->khoiLop->BacHoc ?? ''),
                    'giaSuTen' => $lich->lop->giaSu->HoTen ?? 'Chưa có',
                    'hinhThuc' => $lich->lop->HinhThuc,
                    'trangThai' => $trangThaiText,
                    'duongDan' => $lich->DuongDan,
                    'thoiGianBatDau' => date('H:i', strtotime($lich->ThoiGianBatDau)),
                ]
            ];
        }

        // 6. Trả về view MỚI
        return view('nguoihoc.lich-hoc-show', [
            'calendarDataJson' => json_encode($events),
            'lopHoc' => $lopHoc // Gửi thông tin lớp học sang để hiển thị tiêu đề
        ]);
    }

    /**
     * Hiển thị trang lịch học cho Gia sư
     */
    public function tutorSchedule()
    {
        // 1. Lấy ID Gia sư
        $giaSuId = Auth::user()->giaSu->GiaSuID;

        // 2. Lấy ID các lớp mà gia sư đang dạy
        $lopHocIds = LopHocYeuCau::where('GiaSuID', $giaSuId)
                            ->whereIn('TrangThai', ['DangHoc', 'HoanThanh'])
                            ->pluck('LopYeuCauID');

        // 3. Lấy các buổi học
        $lichHocEvents = LichHoc::whereIn('LopYeuCauID', $lopHocIds)
                            ->with('lop.monHoc', 'lop.nguoiHoc', 'lop.khoiLop')
                            ->get();

        // 4. Bảng màu chỉ chứa MÀU CHỮ
        $colorPalette = [
            '#5B21B6', // Tím
            '#B45309', // Cam/Vàng đậm
            '#065F46', // Xanh lá
            '#1E3A8A', // Xanh dương
            '#9D174D', // Hồng
            '#991B1B', // Đỏ
            '#0F766E', // Teal
        ];
        
        $lopColorMap = [];
        $colorIndex = 0;
        foreach ($lopHocIds as $lopId) {
            $lopColorMap[$lopId] = $colorPalette[$colorIndex % count($colorPalette)];
            $colorIndex++;
        }

        // 5. Định dạng dữ liệu cho calendar
        $events = [];
        $allSchedules = [];
        
        foreach ($lichHocEvents as $lich) {
            $startDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianBatDau;
            $endDateTime = $lich->NgayHoc->format('Y-m-d') . 'T' . $lich->ThoiGianKetThuc;

            // Lấy màu chữ theo lớp
            $textColor = $lopColorMap[$lich->LopYeuCauID] ?? $colorPalette[0];
            
            $trangThaiText = 'SapToi';
            if ($lich->TrangThai == 'DaHoanThanh') {
                $trangThaiText = 'DaHoanThanh';
                $textColor = '#6B7280'; // Màu xám cho chữ
            }

            $events[] = [
                'id' => $lich->LichHocID,
                'title' => $lich->lop->monHoc->TenMon ?? 'Lịch học',
                'start' => $startDateTime,
                'end' => $endDateTime,
                'textColor' => $textColor, 
            ];
            
            // Thêm vào danh sách tất cả lịch học để hiển thị theo ngày
            $allSchedules[] = [
                'date' => $lich->NgayHoc->format('Y-m-d'),
                'lichHocID' => $lich->LichHocID,
                'monHoc' => ($lich->lop->monHoc->TenMon ?? 'N/A') . ' ' . ($lich->lop->khoiLop->BacHoc ?? ''),
                'nguoiHocTen' => $lich->lop->nguoiHoc->HoTen ?? 'Chưa có',
                'hinhThuc' => $lich->lop->HinhThuc,
                'trangThai' => $trangThaiText,
                'duongDan' => $lich->DuongDan,
                'thoiGianBatDau' => date('H:i', strtotime($lich->ThoiGianBatDau)),
                'thoiGianKetThuc' => date('H:i', strtotime($lich->ThoiGianKetThuc)),
            ];
        }

        // 6. Trả về view cho gia sư
        return view('giasu.lich-hoc-index', [
            'calendarDataJson' => json_encode($events),
            'allSchedulesJson' => json_encode($allSchedules)
        ]);
    }
}
