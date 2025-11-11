<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\GiaSu;
use App\Models\NguoiHoc;
use App\Models\LopHocYeuCau;
use App\Models\KhieuNai;
use App\Models\GiaoDich;
use Illuminate\Support\Facades\DB; // <-- Thêm thư viện DB
use Carbon\Carbon; // <-- Thêm thư viện Carbon

class DashboardController extends Controller
{
    public function index() {
        // --- Lấy số liệu đếm cơ bản ---
        $tongGiaSu = GiaSu::count();
        $tongNguoiHoc = NguoiHoc::count();
        $tongLop = LopHocYeuCau::count();
        $tongKhieuNai = KhieuNai::count();
        
        // --- Tính toán Doanh thu thật ---
        
        // 1. Tính tổng doanh thu (chỉ giao dịch 'ThanhCong')
        $totalRevenue = GiaoDich::where('TrangThai', 'ThanhCong')->sum('SoTien');

        // 2. Chuẩn bị dữ liệu cho Biểu đồ (6 tuần gần nhất)
        $weeks = 6;
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subWeeks($weeks - 1)->startOfWeek(); // Bắt đầu từ đầu tuần đầu tiên

        // Lấy tổng doanh thu theo tuần
        $revenueByWeek = GiaoDich::select(
                DB::raw('WEEK(ThoiGian, 1) as week_number'), // 1 = Tuần bắt đầu từ Thứ 2
                DB::raw('YEAR(ThoiGian) as year'),
                DB::raw('SUM(SoTien) as total')
            )
            ->where('TrangThai', 'ThanhCong')
            ->whereBetween('ThoiGian', [$startDate, $endDate])
            ->groupBy('year', 'week_number')
            ->orderBy('year', 'asc')
            ->orderBy('week_number', 'asc')
            ->get()
            ->keyBy(function($item) {
                // Tạo key 'Năm-SốTuần', ví dụ: '2025-45'
                return $item->year . '-' . $item->week_number; 
            });

        $chartLabels = [];
        $chartData = [];
        
        // Tạo nhãn (label) và dữ liệu cho 6 tuần (từ 5 tuần trước đến tuần này)
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $currentWeek = Carbon::now()->subWeeks($i);
            $weekLabel = 'Tuần ' . $currentWeek->weekOfYear; // Ví dụ: "Tuần 45"
            $weekKey = $currentWeek->year . '-' . $currentWeek->weekOfYear;

            $chartLabels[] = $weekLabel;
            // Nếu có dữ liệu cho tuần này thì lấy, không thì 0
            $chartData[] = $revenueByWeek->get($weekKey)->total ?? 0;
        }

        // --- Chuẩn bị dữ liệu cho các thẻ thống kê ---
        // (Phần trăm % tăng/giảm hiện đang là dữ liệu mẫu)
        $statsData = [
            ['title' => 'Tổng Gia Sư', 'count' => $tongGiaSu, 'percent' => '+2.5%', 'color' => 'text-success'],
            ['title' => 'Tổng Người Học', 'count' => $tongNguoiHoc, 'percent' => '+5.8%', 'color' => 'text-success'],
            ['title' => 'Tổng Lớp', 'count' => $tongLop, 'percent' => '+1.2%', 'color' => 'text-success'],
            ['title' => 'Khiếu nại', 'count' => $tongKhieuNai, 'percent' => '-0.5%', 'color' => 'text-danger'],
            // Sử dụng Doanh thu thật ở đây
            ['title' => 'Tổng Doanh Thu', 'count' => $totalRevenue, 'percent' => '+12.1%', 'color' => 'text-success'], 
        ];

        // --- Trả về View với dữ liệu ---
        return view('admin.dashboard', [
            // Dữ liệu cho các thẻ (Stat Cards)
            '_stats' => $statsData,
            
            // Dữ liệu cho Biểu đồ Doanh thu (Line Chart)
            'revenueChartLabels' => $chartLabels,
            'revenueChartData' => $chartData,
            
            // Dữ liệu cho Biểu đồ Phân bố (Doughnut Chart)
            'tongGiaSu' => $tongGiaSu,
            'tongNguoiHoc' => $tongNguoiHoc,
        ]);
    }
}