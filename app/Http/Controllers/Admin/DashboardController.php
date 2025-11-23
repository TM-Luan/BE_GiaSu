<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\GiaSu;       
use App\Models\NguoiHoc;    
use App\Models\LopHocYeuCau; 
use App\Models\KhieuNai;    
use App\Models\GiaoDich;    
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hàm phụ tính phần trăm tăng trưởng
     */
    private function calculateGrowth($currentCount, $previousCount)
    {
        if ($previousCount == 0) {
            return $currentCount > 0 ? 100 : 0;
        }
        return round((($currentCount - $previousCount) / $previousCount) * 100, 1);
    }

    public function index(Request $request) { 
        // 1. Xác định thời gian
        $period = $request->input('period', 30);
        $startDate = Carbon::now()->subDays($period)->startOfDay();
        $prevDate = Carbon::now()->subDays($period * 2)->startOfDay(); // Kỳ trước để so sánh

        // ---------------------------------------------------------
        // 2. Tính toán số liệu THẬT
        // ---------------------------------------------------------

        // --- GIA SƯ (Lấy NgayTao từ bảng TaiKhoan) ---
        $tongGiaSu = GiaSu::count();
        
        // Join bảng GiaSu với TaiKhoan để lọc theo NgayTao của tài khoản
        $giaSuMoiNay = GiaSu::join('TaiKhoan', 'GiaSu.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
                            ->where('TaiKhoan.NgayTao', '>=', $startDate)
                            ->count();
                            
        $giaSuMoiTruoc = GiaSu::join('TaiKhoan', 'GiaSu.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
                              ->whereBetween('TaiKhoan.NgayTao', [$prevDate, $startDate])
                              ->count();
                              
        $pcGiaSu = $this->calculateGrowth($giaSuMoiNay, $giaSuMoiTruoc);


        // --- NGƯỜI HỌC (Lấy NgayTao từ bảng TaiKhoan) ---
        $tongNguoiHoc = NguoiHoc::count();
        
        $hocVienMoiNay = NguoiHoc::join('TaiKhoan', 'NguoiHoc.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
                                 ->where('TaiKhoan.NgayTao', '>=', $startDate)
                                 ->count();
                                 
        $hocVienMoiTruoc = NguoiHoc::join('TaiKhoan', 'NguoiHoc.TaiKhoanID', '=', 'TaiKhoan.TaiKhoanID')
                                   ->whereBetween('TaiKhoan.NgayTao', [$prevDate, $startDate])
                                   ->count();
                                   
        $pcNguoiHoc = $this->calculateGrowth($hocVienMoiNay, $hocVienMoiTruoc);


        // --- LỚP HỌC (Dùng cột NgayTao thay vì NgayYeuCau) ---
        $tongLop = LopHocYeuCau::count();
        $lopMoiNay = LopHocYeuCau::where('NgayTao', '>=', $startDate)->count(); 
        $lopMoiTruoc = LopHocYeuCau::whereBetween('NgayTao', [$prevDate, $startDate])->count(); 
        $pcLop = $this->calculateGrowth($lopMoiNay, $lopMoiTruoc);


        // --- KHIẾU NẠI (Dùng cột NgayTao - Đã đúng) ---
        $tongKhieuNai = KhieuNai::where('NgayTao', '>=', $startDate)->count();
        $khieuNaiTruoc = KhieuNai::whereBetween('NgayTao', [$prevDate, $startDate])->count();
        $pcKhieuNai = $this->calculateGrowth($tongKhieuNai, $khieuNaiTruoc);


        // --- DOANH THU (Dùng cột ThoiGian - Đã đúng) ---
        // Hàm xử lý tiền (xóa dấu chấm/phẩy)
        $parseMoney = function($gd) { 
            return (float) str_replace(['.', ','], '', $gd->SoTien); 
        };

        // Doanh thu kỳ này
        $giaoDichNay = GiaoDich::where('ThoiGian', '>=', $startDate)
            ->where(function($q) { 
                $q->where('TrangThai', 'ThanhCong')
                  ->orWhere('TrangThai', 'Thành công')
                  ->orWhere('TrangThai', 'Success'); 
            })->get();
        $doanhThuNay = $giaoDichNay->sum($parseMoney);

        // Doanh thu kỳ trước
        $giaoDichTruoc = GiaoDich::whereBetween('ThoiGian', [$prevDate, $startDate])
            ->where(function($q) { 
                $q->where('TrangThai', 'ThanhCong')
                  ->orWhere('TrangThai', 'Thành công')
                  ->orWhere('TrangThai', 'Success'); 
            })->get();
        $doanhThuTruoc = $giaoDichTruoc->sum($parseMoney);
        
        $pcDoanhThu = $this->calculateGrowth($doanhThuNay, $doanhThuTruoc);


        // ---------------------------------------------------------
        // 3. Chuẩn bị dữ liệu Biểu đồ
        // ---------------------------------------------------------
        $weeks = 6;
        $chartEndDate = Carbon::now()->endOfDay();
        $chartStartDate = Carbon::now()->subWeeks($weeks - 1)->startOfWeek(); 

        $revenueByWeek = GiaoDich::select(
                DB::raw('WEEK(ThoiGian, 1) as week_number'), 
                DB::raw('YEAR(ThoiGian) as year'), 
                DB::raw('SoTien'), 
                DB::raw('TrangThai')
            )
            ->whereBetween('ThoiGian', [$chartStartDate, $chartEndDate])
            ->where(function($q) { 
                $q->where('TrangThai', 'ThanhCong')
                  ->orWhere('TrangThai', 'Thành công')
                  ->orWhere('TrangThai', 'Success'); 
            })
            ->get()
            ->groupBy(function($item) { return $item->year . '-' . $item->week_number; });

        $chartLabels = [];
        $chartData = [];
        
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $currentWeek = Carbon::now()->subWeeks($i);
            $weekKey = $currentWeek->year . '-' . $currentWeek->weekOfYear;
            $chartLabels[] = 'Tuần ' . $currentWeek->weekOfYear;
            
            if (isset($revenueByWeek[$weekKey])) {
                $chartData[] = $revenueByWeek[$weekKey]->sum($parseMoney);
            } else {
                $chartData[] = 0;
            }
        }

        // ---------------------------------------------------------
        // 4. Trả về View
        // ---------------------------------------------------------
        $formatPc = function($val) {
            $sign = $val > 0 ? '+' : ''; 
            $color = $val >= 0 ? 'text-success' : 'text-danger';
            return ['percent' => $sign . $val . '%', 'color' => $color];
        };

        $statsData = [
            array_merge(['title' => 'Tổng Gia Sư', 'count' => $tongGiaSu], $formatPc($pcGiaSu)),
            array_merge(['title' => 'Tổng Người Học', 'count' => $tongNguoiHoc], $formatPc($pcNguoiHoc)),
            array_merge(['title' => 'Tổng Lớp', 'count' => $tongLop], $formatPc($pcLop)),
            array_merge(['title' => 'Khiếu nại', 'count' => $tongKhieuNai], $formatPc($pcKhieuNai)), 
            array_merge(['title' => 'Tổng Doanh Thu', 'count' => $doanhThuNay], $formatPc($pcDoanhThu)), 
        ];

        return view('admin.dashboard', [
            '_stats' => $statsData,
            'revenueChartLabels' => $chartLabels,
            'revenueChartData' => $chartData,
            'tongGiaSu' => $tongGiaSu,
            'tongNguoiHoc' => $tongNguoiHoc,
        ]);
    }
}