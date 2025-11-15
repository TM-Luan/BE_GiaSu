<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiaSuController;
use App\Http\Controllers\NguoiHocController;
use App\Http\Controllers\LopHocYeuCauController;
use App\Http\Controllers\YeuCauNhanLopController;
use App\Http\Controllers\LichHocController;
use App\Http\Controllers\DropdownDataController;
use App\Http\Controllers\KhieuNaiController;
use App\Http\Controllers\DanhGiaController;

// Admin API Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GiaSuController as AdminGiaSuController;
use App\Http\Controllers\Admin\NguoiHocController as AdminNguoiHocController;
use App\Http\Controllers\Admin\TaiKhoanController as AdminTaiKhoanController;
use App\Http\Controllers\Admin\KhieuNaiController as AdminKhieuNaiController;
use App\Http\Controllers\Admin\GiaoDichController as AdminGiaoDichController;
use App\Http\Controllers\Admin\LopHocController as AdminLopHocController;
use App\Http\Controllers\Admin\LichHocController as AdminLichHocController;

/*
|--------------------------------------------------------------------------
| API Routes (Mobile App & External Integrations)
|--------------------------------------------------------------------------
| 
| Tất cả routes ở đây trả về JSON responses
| Không có Blade views hay form submissions
|
*/

// ===== PUBLIC API ROUTES =====

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resetpassword', [AuthController::class, 'resetPassword']);

// Search & Filter (Public)
Route::get('/giasu/search', [GiaSuController::class, 'search']);
Route::get('/lophoc/search', [LopHocYeuCauController::class, 'search']);
Route::get('/filter-options', [DropdownDataController::class, 'getFilterOptions']);
Route::get('/search-stats', [DropdownDataController::class, 'getSearchStats']);
Route::get('/search-suggestions', [DropdownDataController::class, 'getSearchSuggestions']);

// Dropdown data (Public)
Route::get('/monhoc', [DropdownDataController::class, 'getMonHocList']);
Route::get('/khoilop', [DropdownDataController::class, 'getKhoiLopList']);
Route::get('/doituong', [DropdownDataController::class, 'getDoiTuongList']);
Route::get('/thoigianday', [DropdownDataController::class, 'getThoiGianDayList']);

// DanhGia (Public) - Xem đánh giá gia sư
Route::get('/giasu/{giaSuId}/danhgia', [DanhGiaController::class, 'getDanhGiaGiaSu']);

// Resource routes (Public read-only)
Route::get('/giasu', [GiaSuController::class, 'index']);
Route::get('/giasu/{id}', [GiaSuController::class, 'show']);
Route::get('/lophocyeucau', [LopHocYeuCauController::class, 'index']);
Route::get('/lophocyeucau/{id}', [LopHocYeuCauController::class, 'show']);


// ===== AUTHENTICATED API ROUTES =====
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Profile Management
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/changepassword', [AuthController::class, 'changePassword']);
    
    // Khiếu nại
    Route::get('/khieunai', [KhieuNaiController::class, 'index']);
    Route::post('/khieunai', [KhieuNaiController::class, 'store']);

    // Đánh giá gia sư
    Route::post('/danhgia', [DanhGiaController::class, 'taoDanhGia']);
    Route::get('/giasu/{giaSuId}/kiem-tra-danh-gia', [DanhGiaController::class, 'kiemTraDaDanhGia']);
    Route::delete('/danhgia/{danhGiaId}', [DanhGiaController::class, 'xoaDanhGia']);

    // Lịch học - Quản lý
    Route::post('/lop/{lopYeuCauId}/lich-hoc-lap-lai', [LichHocController::class, 'taoLichHocLapLai']);
    Route::put('/lich-hoc/{lichHocId}', [LichHocController::class, 'capNhatLichHocGiaSu']);
    Route::delete('/lich-hoc/{lichHocId}', [LichHocController::class, 'xoaLichHoc']);

    // Lịch học - Xem theo tháng
    Route::get('/giasu/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangGiaSu']);
    Route::get('/nguoihoc/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangNguoiHoc']);
    Route::get('/lop/{lopYeuCauId}/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoLopVaThang']);
    
    // Lịch học - Calendar API
    Route::get('/giasu/lich-hoc-summary', [LichHocController::class, 'getLichHocSummaryGiaSu']);
    Route::get('/giasu/lich-hoc-theo-ngay', [LichHocController::class, 'getLichHocTheoNgayGiaSu']);
    Route::get('/nguoihoc/lich-hoc-summary', [LichHocController::class, 'getLichHocSummaryNguoiHoc']);
    Route::get('/nguoihoc/lich-hoc-theo-ngay', [LichHocController::class, 'getLichHocTheoNgayNguoiHoc']);

    // Yêu cầu nhận lớp (Gia sư ↔ Người học)
    Route::post('/giasu/guiyeucau', [YeuCauNhanLopController::class, 'giaSuGuiYeuCau']);
    Route::post('/nguoihoc/moigiasu', [YeuCauNhanLopController::class, 'nguoiHocMoiGiaSu']);
    Route::put('/yeucau/{yeuCauID}', [YeuCauNhanLopController::class, 'capNhatYeuCau']);
    Route::put('/yeucau/{yeuCauID}/xacnhan', [YeuCauNhanLopController::class, 'xacNhanYeuCau']);
    Route::put('/yeucau/{yeuCauID}/tuchoi', [YeuCauNhanLopController::class, 'tuChoiYeuCau']);
    Route::delete('/yeucau/{yeuCauID}/huy', [YeuCauNhanLopController::class, 'huyYeuCau']);
    Route::get('/yeucau/dagui', [YeuCauNhanLopController::class, 'danhSachYeuCauDaGui']);
    Route::get('/yeucau/nhanduoc', [YeuCauNhanLopController::class, 'danhSachYeuCauNhanDuoc']);
    Route::get('/lophocyeucau/{lopYeuCauID}/de-nghi', [YeuCauNhanLopController::class, 'danhSachDeNghiTheoLop']);
    Route::get('/giasu/{giaSuID}/lop', [YeuCauNhanLopController::class, 'getLopCuaGiaSu']);

    // Người học - Lớp học của tôi
    Route::get('/nguoihoc/lopcuatoi', [NguoiHocController::class, 'getLopHocCuaNguoiHoc']);
    
    // Lớp học yêu cầu - CRUD
    Route::post('/lophocyeucau', [LopHocYeuCauController::class, 'store']);
    Route::put('/lophocyeucau/{id}', [LopHocYeuCauController::class, 'update']);
    Route::delete('/lophocyeucau/{id}', [LopHocYeuCauController::class, 'destroy']);

    // ===== ADMIN API ROUTES =====
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        
        // Dashboard & Statistics
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        
        // Quản lý Gia sư
        Route::apiResource('giasu', AdminGiaSuController::class);
        Route::get('/giasu/pending', [AdminGiaSuController::class, 'pendingList']);
        Route::put('/giasu/{id}/approve', [AdminGiaSuController::class, 'approveProfile']);
        Route::put('/giasu/{id}/reject', [AdminGiaSuController::class, 'rejectProfile']);
        
        // Quản lý Người học
        Route::apiResource('nguoihoc', AdminNguoiHocController::class);
        
        // Quản lý Tài khoản
        Route::get('/taikhoan', [AdminTaiKhoanController::class, 'index']);
        
        // Quản lý Khiếu nại
        Route::get('/khieunai', [AdminKhieuNaiController::class, 'index']);
        Route::get('/khieunai/statistics', [AdminKhieuNaiController::class, 'statistics']);
        Route::get('/khieunai/{id}', [AdminKhieuNaiController::class, 'show']);
        Route::put('/khieunai/{id}/status', [AdminKhieuNaiController::class, 'updateStatus']);
        Route::delete('/khieunai/{id}', [AdminKhieuNaiController::class, 'destroy']);
        
        // Quản lý Giao dịch
        Route::get('/giaodich', [AdminGiaoDichController::class, 'index']);
        Route::get('/giaodich/statistics', [AdminGiaoDichController::class, 'statistics']);
        Route::get('/giaodich/export', [AdminGiaoDichController::class, 'export']);
        Route::get('/giaodich/{id}', [AdminGiaoDichController::class, 'show']);
        Route::put('/giaodich/{id}/status', [AdminGiaoDichController::class, 'updateStatus']);
        Route::delete('/giaodich/{id}', [AdminGiaoDichController::class, 'destroy']);
        
        // Quản lý Lớp học
        Route::get('/lophoc', [AdminLopHocController::class, 'index']);
        Route::get('/lophoc/statistics', [AdminLopHocController::class, 'statistics']);
        Route::get('/lophoc/{id}', [AdminLopHocController::class, 'show']);
        Route::put('/lophoc/{id}', [AdminLopHocController::class, 'update']);
        Route::put('/lophoc/{id}/status', [AdminLopHocController::class, 'updateStatus']);
        Route::delete('/lophoc/{id}', [AdminLopHocController::class, 'destroy']);
        Route::get('/lophoc/giasu/{giaSuId}', [AdminLopHocController::class, 'getByGiaSu']);
        Route::get('/lophoc/nguoihoc/{nguoiHocId}', [AdminLopHocController::class, 'getByNguoiHoc']);
        
        // Quản lý Lịch học
        Route::get('/lichhoc', [AdminLichHocController::class, 'index']);
        Route::get('/lichhoc/statistics', [AdminLichHocController::class, 'statistics']);
        Route::get('/lichhoc/calendar', [AdminLichHocController::class, 'getCalendar']);
        Route::get('/lichhoc/{id}', [AdminLichHocController::class, 'show']);
        Route::put('/lichhoc/{id}', [AdminLichHocController::class, 'update']);
        Route::put('/lichhoc/{id}/status', [AdminLichHocController::class, 'updateStatus']);
        Route::delete('/lichhoc/{id}', [AdminLichHocController::class, 'destroy']);
        Route::get('/lichhoc/lop/{lopId}', [AdminLichHocController::class, 'getByLop']);
    });
});