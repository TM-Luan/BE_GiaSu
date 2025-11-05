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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resetpassword', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('/changepassword', [AuthController::class, 'changePassword']);

    // LichHoc routes - Tạo và quản lý lịch học
    Route::post('/lop/{lopYeuCauId}/lich-hoc-lap-lai', [LichHocController::class, 'taoLichHocLapLai']);
    Route::put('/lich-hoc/{lichHocId}', [LichHocController::class, 'capNhatLichHocGiaSu']);
    Route::delete('/lich-hoc/{lichHocId}', [LichHocController::class, 'xoaLichHoc']);

    // LichHoc routes - Hiển thị lịch học theo tháng
    Route::get('/giasu/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangGiaSu']);
    Route::get('/nguoihoc/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoThangNguoiHoc']);
    Route::get('/lop/{lopYeuCauId}/lich-hoc-theo-thang', [LichHocController::class, 'getLichHocTheoLopVaThang']);

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
});

// Search & Filter routes (public)
Route::get('/giasu/search', [GiaSuController::class, 'search']);
Route::get('/lophoc/search', [LopHocYeuCauController::class, 'search']);
Route::get('/filter-options', [DropdownDataController::class, 'getFilterOptions']);
Route::get('/search-stats', [DropdownDataController::class, 'getSearchStats']);
Route::get('/search-suggestions', [DropdownDataController::class, 'getSearchSuggestions']);

// Dropdown data routes (public)
Route::get('/monhoc', [DropdownDataController::class, 'getMonHocList']);
Route::get('/khoilop', [DropdownDataController::class, 'getKhoiLopList']);
Route::get('/doituong', [DropdownDataController::class, 'getDoiTuongList']);
Route::get('/thoigianday', [DropdownDataController::class, 'getThoiGianDayList']);

// Resource routes
Route::resource('nguoihoc', NguoiHocController::class);
Route::resource('giasu', GiaSuController::class);
Route::resource('lophocyeucau', LopHocYeuCauController::class);
