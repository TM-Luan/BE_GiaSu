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

        // 2. Lấy ID các lớp (ĐỒNG BỘ: Hiển thị tất cả lịch học có gia sư)
        $lopHocIds = LopHocYeuCau::where('NguoiHocID', $nguoiHocId)
                            ->whereNotNull('GiaSuID') // Chỉ lấy lớp đã có gia sư
                            ->pluck('LopYeuCauID');

        // 3. Lấy các buổi học
        $lichHocEvents = LichHoc::whereIn('LopYeuCauID', $lopHocIds)
                            ->with('lop.monHoc', 'lop.giaSu', 'lop.khoiLop')
                            ->get();

        // 4. Format dữ liệu cho calendar view theo tuần
        $scheduleData = [];
        foreach ($lichHocEvents as $lich) {
            $scheduleData[] = [
                'id' => $lich->LichHocID,
                'date' => $lich->NgayHoc->format('Y-m-d'),
                'time' => date('H:i', strtotime($lich->ThoiGianBatDau)),
                'subject' => $lich->lop->monHoc->TenMon ?? 'N/A',
                'tutor' => $lich->lop->giaSu->HoTen ?? 'N/A',
                'isOnline' => $lich->lop->HinhThuc === 'Online',
                'link' => $lich->DuongDan,
                'status' => $lich->TrangThai,
                'grade' => $lich->lop->khoiLop->BacHoc ?? ''
            ];
        }

        return view('nguoihoc.lich-hoc-calendar', [
            'scheduleDataJson' => json_encode($scheduleData)
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

        // 4. Format dữ liệu cho calendar view theo tuần
        $scheduleData = [];
        foreach ($lichHocEvents as $lich) {
            $scheduleData[] = [
                'id' => $lich->LichHocID,
                'date' => $lich->NgayHoc->format('Y-m-d'),
                'time' => date('H:i', strtotime($lich->ThoiGianBatDau)),
                'subject' => $lich->lop->monHoc->TenMon ?? 'N/A',
                'student' => $lich->lop->nguoiHoc->HoTen ?? 'N/A',
                'isOnline' => $lich->lop->HinhThuc === 'Online',
                'link' => $lich->DuongDan,
                'status' => $lich->TrangThai,
                'grade' => $lich->lop->khoiLop->BacHoc ?? ''
            ];
        }

        return view('giasu.lich-hoc-calendar', [
            'scheduleDataJson' => json_encode($scheduleData)
        ]);
    }
}
