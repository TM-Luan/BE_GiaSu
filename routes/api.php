<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiaSuController;
use App\Http\Controllers\NguoiHocController;
use App\Http\Controllers\LopHocYeuCauController;
use App\Http\Controllers\YeuCauNhanLopController;
use App\Http\Controllers\LichHocController;
use App\Http\Controllers\DropdownDataController;
use App\Http\Controllers\KhieuNaiController;
use App\Http\Controllers\DanhGiaController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GiaSuController as AdminGiaSuController;
use App\Http\Controllers\Admin\NguoiHocController as AdminNguoiHocController;
use App\Http\Controllers\Admin\TaiKhoanController as AdminTaiKhoanController;
use App\Http\Controllers\Admin\KhieuNaiController as AdminKhieuNaiController;
use App\Http\Controllers\Admin\GiaoDichController as AdminGiaoDichController;
use App\Http\Controllers\Admin\LopHocController as AdminLopHocController;
use App\Http\Controllers\Admin\LichHocController as AdminLichHocController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resetpassword', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('/changepassword', [AuthController::class, 'changePassword']);
    Route::get('/khieunai', [KhieuNaiController::class, 'index']);
    Route::post('/khieunai', [KhieuNaiController::class, 'store']);

    // DanhGia routes - Đánh giá gia sư
    Route::post('/danhgia', [DanhGiaController::class, 'taoDanhGia']);
    Route::get('/giasu/{giaSuId}/kiem-tra-danh-gia', [DanhGiaController::class, 'kiemTraDaDanhGia']);
    Route::delete('/danhgia/{danhGiaId}', [DanhGiaController::class, 'xoaDanhGia']);

    // LichHoc routes - Tạo và quản lý lịch học
    Route::post('/lop/{lopYeuCauId}/lich-hoc-lap-lai', [LichHocController::class, 'taoLichHocLapLai']);
    Route::put('/lich-hoc/{lichHocId}', [LichHocController::class, 'capNhatLichHocGiaSu']);
    Route::delete('/lich-hoc/{lichHocId}', [LichHocController::class, 'xoaLichHoc']);

    // LichHoc routes - Hiển thị lịch học theo tháng
    Route::get('/giasu/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangGiaSu']);
    Route::get('/nguoihoc/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangNguoiHoc']);
    Route::get('/lop/{lopYeuCauId}/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoLopVaThang']);
    
    // LichHoc routes - API mới cho Calendar (Summary + Chi tiết theo ngày)
    Route::get('/giasu/lich-hoc-summary', [LichHocController::class, 'getLichHocSummaryGiaSu']);
    Route::get('/giasu/lich-hoc-theo-ngay', [LichHocController::class, 'getLichHocTheoNgayGiaSu']);
    Route::get('/nguoihoc/lich-hoc-summary', [LichHocController::class, 'getLichHocSummaryNguoiHoc']);
    Route::get('/nguoihoc/lich-hoc-theo-ngay', [LichHocController::class, 'getLichHocTheoNgayNguoiHoc']);

    // YeuCauNhanLop routes
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

    // User specific routes
    Route::get('/nguoihoc/lopcuatoi', [NguoiHocController::class, 'getLopHocCuaNguoiHoc']);
    Route::put('/lophocyeucau/{id}', [LopHocYeuCauController::class, 'update']);

    // ===== ADMIN ROUTES =====
    // Middleware kiểm tra vai trò admin cần được thêm vào sau
    Route::prefix('admin')->group(function () {
        
        // Dashboard & Statistics
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        
        // Quản lý Gia sư
        Route::get('/giasu', [AdminGiaSuController::class, 'index']);
        Route::get('/giasu/pending', [AdminGiaSuController::class, 'pendingList']); // Danh sách chờ duyệt
        Route::get('/giasu/{id}', [AdminGiaSuController::class, 'show']);
        Route::post('/giasu', [AdminGiaSuController::class, 'store']);
        Route::put('/giasu/{id}', [AdminGiaSuController::class, 'update']);
        Route::delete('/giasu/{id}', [AdminGiaSuController::class, 'destroy']);
        Route::put('/giasu/{id}/approve', [AdminGiaSuController::class, 'approveProfile']); // Duyệt hồ sơ
        Route::put('/giasu/{id}/reject', [AdminGiaSuController::class, 'rejectProfile']); // Từ chối hồ sơ
        
        // Quản lý Người học
        Route::get('/nguoihoc', [AdminNguoiHocController::class, 'index']);
        Route::get('/nguoihoc/{id}', [AdminNguoiHocController::class, 'show']);
        Route::post('/nguoihoc', [AdminNguoiHocController::class, 'store']);
        Route::put('/nguoihoc/{id}', [AdminNguoiHocController::class, 'update']);
        Route::delete('/nguoihoc/{id}', [AdminNguoiHocController::class, 'destroy']);
        
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

// Search & Filter routes (public)
Route::get('/giasu/search', [GiaSuController::class, 'search']);
Route::get('/lophoc/search', [LopHocYeuCauController::class, 'search']);
Route::get('/filter-options', [DropdownDataController::class, 'getFilterOptions']);
Route::get('/search-stats', [DropdownDataController::class, 'getSearchStats']);
Route::get('/search-suggestions', [DropdownDataController::class, 'getSearchSuggestions']);

// DanhGia routes (public) - Xem đánh giá gia sư
Route::get('/giasu/{giaSuId}/danhgia', [DanhGiaController::class, 'getDanhGiaGiaSu']);

// Dropdown data routes (public)
Route::get('/monhoc', [DropdownDataController::class, 'getMonHocList']);
Route::get('/khoilop', [DropdownDataController::class, 'getKhoiLopList']);
Route::get('/doituong', [DropdownDataController::class, 'getDoiTuongList']);
Route::get('/thoigianday', [DropdownDataController::class, 'getThoiGianDayList']);

// Resource routes
Route::resource('nguoihoc', NguoiHocController::class);
Route::resource('giasu', GiaSuController::class);
Route::resource('lophocyeucau', LopHocYeuCauController::class);