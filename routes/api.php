<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GiaSuController;
use App\Http\Controllers\Api\NguoiHocController;
use App\Http\Controllers\Api\LopHocYeuCauController;
use App\Http\Controllers\Api\YeuCauNhanLopController;
use App\Http\Controllers\Api\LichHocController;
use App\Http\Controllers\Api\DropdownDataController;
use App\Http\Controllers\Api\KhieuNaiController;
use App\Http\Controllers\Api\DanhGiaController;
use App\Http\Controllers\Api\GiaoDichController;
use App\Http\Controllers\Api\NotificationController;

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
    Route::put('/khieunai/{id}', [KhieuNaiController::class, 'update']);
    Route::delete('/khieunai/{id}', [KhieuNaiController::class, 'destroy']);

    // DanhGia routes - Đánh giá gia sư
    Route::post('/danhgia', [DanhGiaController::class, 'taoDanhGia']);
    Route::get('/giasu/{giaSuId}/kiem-tra-danh-gia', [DanhGiaController::class, 'kiemTraDaDanhGia']);
    Route::delete('/danhgia/{danhGiaId}', [DanhGiaController::class, 'xoaDanhGia']);

    // LichHoc routes - Tạo và quản lý lịch học
    Route::post('/lop/{lopYeuCauId}/lich-hoc-lap-lai', [LichHocController::class, 'taoLichHocLapLai']);
    Route::put('/lich-hoc/{lichHocId}', [LichHocController::class, 'capNhatLichHocGiaSu']);
    Route::delete('/lich-hoc/{lichHocId}', [LichHocController::class, 'xoaLichHoc']);
     Route::post('/lop/{lopYeuCauId}/tao-lich-theo-tuan', [LichHocController::class, 'taoNhieuLichHocTheoTuan']);
     Route::delete('/lop/{lopYeuCauId}/xoa-tat-ca-lich', [LichHocController::class, 'xoaTatCaLichHocTheoLop']);


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
    Route::post('/giao-dich', [GiaoDichController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    
    // Đánh dấu đã đọc 1 thông báo
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});
// Search & Filter routes (public)
Route::get('/giasu/search', [GiaSuController::class, 'search']);
Route::get('/lophoc/search', [LopHocYeuCauController::class, 'search']);
Route::get('/filter-options', [DropdownDataController::class, 'getFilterOptions']);
Route::get('/search-stats', [DropdownDataController::class, 'getSearchStats']);
Route::get('/search-suggestions', [DropdownDataController::class, 'getSearchSuggestions']);

Route::get('/giasu/{giaSuId}/danhgia', [DanhGiaController::class, 'getDanhGiaCongKhaiCuaGiaSu']);
// Dropdown data routes (public)
Route::get('/monhoc', [DropdownDataController::class, 'getMonHocList']);
Route::get('/khoilop', [DropdownDataController::class, 'getKhoiLopList']);
Route::get('/doituong', [DropdownDataController::class, 'getDoiTuongList']);
Route::get('/thoigianday', [DropdownDataController::class, 'getThoiGianDayList']);

// Resource routes
Route::resource('nguoihoc', NguoiHocController::class);
Route::resource('giasu', GiaSuController::class);
Route::resource('lophocyeucau', LopHocYeuCauController::class);